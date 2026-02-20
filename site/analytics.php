<?php
/**
 * Access Log Analyser — PHP edition
 *
 * Reads the log written by logger.php (Apache combined format) and writes
 * one report-YYYY-MM-DD.json per day into a data directory.
 * Output is identical to what analytics.py produces, so stats.html works
 * with either parser — or both on different servers.
 *
 * ── Setup ────────────────────────────────────────────────────────────
 * Add to .htaccess:
 *
 *   SetEnv ANALYTICS_TOKEN  change-this-secret
 *   SetEnv LOGGER_LOG_FILE  /home/user/logs/access-self.log
 *   SetEnv ANALYTICS_DATA   /home/user/public_html/data/analytics
 *
 * ── Run ──────────────────────────────────────────────────────────────
 *   https://yoursite.com/analytics.php?token=change-this-secret
 *
 * Optional query params:
 *   &append=1    merge into existing reports instead of overwriting
 *   &rotate=1    after writing reports, truncate the log file (use at month end)
 *   &dry=1       parse and print summary without writing any files
 *
 * Recommended monthly workflow:
 *   1. Visit analytics.php?token=…&rotate=1  (processes log, then clears it)
 *   2. The JSON reports are permanent; the raw log starts fresh next month
 *   3. On VPS with real logs, rotate=1 is a no-op — manage logs via logrotate
 */

// ── Configuration ────────────────────────────────────────────────────

$TOKEN        = getenv('ANALYTICS_TOKEN') ?: 'change-this-secret';
$LOG_FILE     = getenv('LOGGER_LOG_FILE') ?: __DIR__ . '/access.log';
$DATA_DIR     = getenv('ANALYTICS_DATA')  ?: __DIR__ . '/data/analytics';

// ── Auth ─────────────────────────────────────────────────────────────

$given = $_GET['token'] ?? '';
if (!hash_equals($TOKEN, $given)) {
    http_response_code(403);
    exit('Forbidden');
}

// ── Options ──────────────────────────────────────────────────────────

$append = isset($_GET['append']) && $_GET['append'] !== '0';
$rotate = isset($_GET['rotate']) && $_GET['rotate'] !== '0';
$dry    = isset($_GET['dry'])    && $_GET['dry']    !== '0';

// ── Helpers ──────────────────────────────────────────────────────────

/**
 * Parse one Apache combined log line.
 * Returns an array with keys [date, path, referrer, visitor_token]
 * or null if the line should be skipped.
 */
function parse_line(string $line): ?array
{
    static $pattern = '/^(\S+) \S+ \S+ \[([^\]]+)\] "(\S+) (\S+) \S+" (\d+) \S+ "([^"]*)" "([^"]*)"/';

    if (!preg_match($pattern, $line, $m)) {
        return null;
    }

    [, $ip, $timestamp, $method, $path, $status, $referrer, $ua] = $m;

    // Only GET/HEAD
    if ($method !== 'GET' && $method !== 'HEAD') {
        return null;
    }

    // Only successful responses
    if ((int)$status >= 400) {
        return null;
    }

    // Skip static assets — only log page-like paths
    $ext = strtolower(pathinfo(strtok($path, '?'), PATHINFO_EXTENSION));
    $page_exts = ['', 'php', 'html', 'htm'];
    if ($ext !== '' && !in_array($ext, $page_exts, true)) {
        return null;
    }

    // Skip bots
    $ua_lower = strtolower($ua);
    $bot_words = ['bot', 'crawl', 'spider', 'slurp', 'baidu', 'yandex',
                  'duckduck', 'ia_archiver', 'facebookexternalhit',
                  'semrush', 'ahrefsbot', 'mj12bot', 'dotbot', 'petalbot'];
    foreach ($bot_words as $word) {
        if (strpos($ua_lower, $word) !== false) {
            return null;
        }
    }

    // Parse date from timestamp: "20/Feb/2026:19:00:00 +0000"
    $dt = DateTime::createFromFormat('d/M/Y:H:i:s O', $timestamp);
    if (!$dt) {
        $dt = DateTime::createFromFormat('d/M/Y:H:i:s', explode(' ', $timestamp)[0]);
    }
    if (!$dt) {
        return null;
    }

    $date = $dt->format('Y-m-d');

    // Strip query string from path
    $clean_path = strtok($path, '?');

    // Visitor token: hash of IP + UA + date (no raw PII stored)
    $visitor_token = hash('sha256', $ip . $ua . $date);

    // Clean referrer to domain only
    $ref = clean_referrer($referrer);

    return [
        'date'          => $date,
        'path'          => $clean_path,
        'referrer'      => $ref,
        'visitor_token' => $visitor_token,
    ];
}

function clean_referrer(string $raw): string
{
    if ($raw === '' || $raw === '-') {
        return 'Direct';
    }
    if (preg_match('#https?://([^/]+)#', $raw, $m)) {
        $domain = $m[1];
        if (strncmp($domain, 'www.', 4) === 0) {
            $domain = substr($domain, 4);
        }
        return $domain;
    }
    return 'Direct';
}

// ── Parse log ────────────────────────────────────────────────────────

if (!file_exists($LOG_FILE)) {
    http_response_code(500);
    exit('Log file not found: ' . htmlspecialchars($LOG_FILE));
}

$daily = [];   // [ 'YYYY-MM-DD' => [ visitors=>set, pageviews=>n, pages=>[], referrers=>[] ] ]
$total = 0;
$skipped = 0;

$fh = fopen($LOG_FILE, 'r');
if (!$fh) {
    http_response_code(500);
    exit('Cannot open log file.');
}

while (($line = fgets($fh)) !== false) {
    $entry = parse_line(rtrim($line));
    if ($entry === null) {
        $skipped++;
        continue;
    }

    $d = $entry['date'];
    if (!isset($daily[$d])) {
        $daily[$d] = [
            'visitors'  => [],   // token => true
            'pageviews' => 0,
            'pages'     => [],   // path => count
            'referrers' => [],   // source => count
        ];
    }

    $daily[$d]['visitors'][$entry['visitor_token']] = true;
    $daily[$d]['pageviews']++;
    $daily[$d]['pages'][$entry['path']]         = ($daily[$d]['pages'][$entry['path']]         ?? 0) + 1;
    $daily[$d]['referrers'][$entry['referrer']] = ($daily[$d]['referrers'][$entry['referrer']] ?? 0) + 1;
    $total++;
}
fclose($fh);

// ── Build & write reports ─────────────────────────────────────────────

if (!$dry && !is_dir($DATA_DIR)) {
    mkdir($DATA_DIR, 0755, true);
}

ksort($daily);
$written = 0;

foreach ($daily as $date => $stats) {
    // Sort pages and referrers descending
    arsort($stats['pages']);
    arsort($stats['referrers']);

    $report = [
        'date'    => $date,
        'summary' => [
            'visitors'  => count($stats['visitors']),
            'pageviews' => $stats['pageviews'],
        ],
        'pages' => array_slice(
            array_map(
                fn($path, $views) => ['path' => $path, 'views' => $views],
                array_keys($stats['pages']),
                array_values($stats['pages'])
            ),
            0, 20
        ),
        'referrers' => array_slice(
            array_map(
                fn($source, $visits) => ['source' => $source, 'visits' => $visits],
                array_keys($stats['referrers']),
                array_values($stats['referrers'])
            ),
            0, 15
        ),
    ];

    if ($dry) {
        continue;
    }

    $out_file = $DATA_DIR . '/report-' . $date . '.json';

    if ($append && file_exists($out_file)) {
        $existing = json_decode(file_get_contents($out_file), true);
        if (is_array($existing)) {
            $report = merge_reports($existing, $report);
        }
    }

    file_put_contents($out_file, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $written++;
}

// ── Merge helper ─────────────────────────────────────────────────────

function merge_reports(array $existing, array $new): array
{
    $existing['summary']['visitors']  += $new['summary']['visitors'];
    $existing['summary']['pageviews'] += $new['summary']['pageviews'];

    $pages = [];
    foreach ($existing['pages'] as $p) {
        $pages[$p['path']] = $p['views'];
    }
    foreach ($new['pages'] as $p) {
        $pages[$p['path']] = ($pages[$p['path']] ?? 0) + $p['views'];
    }
    arsort($pages);
    $existing['pages'] = array_slice(
        array_map(fn($k, $v) => ['path' => $k, 'views' => $v], array_keys($pages), $pages),
        0, 20
    );

    $refs = [];
    foreach ($existing['referrers'] as $r) {
        $refs[$r['source']] = $r['visits'];
    }
    foreach ($new['referrers'] as $r) {
        $refs[$r['source']] = ($refs[$r['source']] ?? 0) + $r['visits'];
    }
    arsort($refs);
    $existing['referrers'] = array_slice(
        array_map(fn($k, $v) => ['source' => $k, 'visits' => $v], array_keys($refs), $refs),
        0, 15
    );

    return $existing;
}

// ── Write index.json — list of all available report dates ────────────

if (!$dry) {
    $all_dates = [];
    foreach (glob($DATA_DIR . '/report-????-??-??.json') as $f) {
        if (preg_match('/report-(\d{4}-\d{2}-\d{2})\.json$/', $f, $m)) {
            $all_dates[] = $m[1];
        }
    }
    sort($all_dates);
    file_put_contents(
        $DATA_DIR . '/index.json',
        json_encode($all_dates, JSON_PRETTY_PRINT)
    );
}

// ── Rotate: truncate log after successful write ───────────────────────

$rotated = false;
if ($rotate && !$dry && $written > 0) {
    // Truncate rather than delete — keeps the file handle valid for logger.php
    $fh = fopen($LOG_FILE, 'w');
    if ($fh) {
        fclose($fh);
        $rotated = true;
    }
}

// ── Output ────────────────────────────────────────────────────────────

header('Content-Type: text/plain; charset=utf-8');
echo "Access Log Analyser\n";
echo "===================\n";
echo "Log file : {$LOG_FILE}\n";
echo "Data dir : {$DATA_DIR}\n";
$mode = $dry ? 'dry run' : ($append ? 'append' : 'overwrite');
if ($rotate) $mode .= $rotated ? ' + rotate' : ' + rotate (skipped — no reports written)';
echo "Mode     : {$mode}\n";
echo "\n";
echo "Lines parsed   : {$total}\n";
echo "Lines skipped  : {$skipped}\n";
echo "Days found     : " . count($daily) . "\n";
echo "Reports written: {$written}\n";
if ($rotated) {
    echo "Log truncated  : yes (starts fresh)\n";
}
echo "\nDone.\n";
