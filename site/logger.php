<?php
/**
 * Self-hosted access logger
 *
 * Writes one line per PHP request in Apache combined log format so the output
 * is compatible with analytics.py and any standard log parser.
 *
 * Activate via .htaccess (add ONE of the lines below, uncommented):
 *   php_value auto_prepend_file "/absolute/path/to/logger.php"
 *   php_value auto_prepend_file "logger.php"   (if in the same directory)
 *
 * Log location: configured by LOG_FILE below, or override with the
 * LOGGER_LOG_FILE environment variable set in .htaccess:
 *   SetEnv LOGGER_LOG_FILE /home/user/logs/access.log
 *
 * Format (Apache combined log):
 *   IP - - [DD/Mon/YYYY:HH:MM:SS +0000] "METHOD /path HTTP/1.1" STATUS SIZE "REFERRER" "UA"
 *
 * Notes:
 *  - Only PHP-served requests are logged (static files are not captured).
 *  - Response size is always "-" at prepend time; a shutdown function fills it
 *    in where ob_get_length() is available.
 *  - The log file is appended to with LOCK_EX to avoid corruption under load.
 *  - Bots and crawlers are NOT filtered here; filter at parse time (analytics.py
 *    already does this).
 */

(function () {
    // -------------------------------------------------------------------------
    // Configuration
    // -------------------------------------------------------------------------

    // Default log path. Override with SetEnv LOGGER_LOG_FILE in .htaccess.
    // Use an absolute path outside public_html when possible.
    $logFile = getenv('LOGGER_LOG_FILE') ?: __DIR__ . '/access.log';

    // -------------------------------------------------------------------------
    // Capture request fields immediately (values may change later)
    // -------------------------------------------------------------------------
    $ip        = $_SERVER['REMOTE_ADDR']     ?? '-';
    $method    = $_SERVER['REQUEST_METHOD']  ?? 'GET';
    $uri       = $_SERVER['REQUEST_URI']     ?? '/';
    $proto     = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/1.1';
    $referrer  = $_SERVER['HTTP_REFERER']    ?? '-';
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '-';
    $timestamp = date('d/M/Y:H:i:s O');

    // -------------------------------------------------------------------------
    // Ensure log directory exists
    // -------------------------------------------------------------------------
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        @mkdir($logDir, 0750, true);
    }

    // -------------------------------------------------------------------------
    // Write the log line via a shutdown function so we can capture the HTTP
    // status code (set by header() calls in the page) and output size.
    // -------------------------------------------------------------------------
    register_shutdown_function(function () use (
        $logFile, $ip, $method, $uri, $proto,
        $referrer, $userAgent, $timestamp
    ) {
        $status = http_response_code();
        if ($status === false || $status === null) {
            $status = 200;
        }

        // Output size: use output buffer length if available, else "-"
        $size = ob_get_length();
        $size = ($size !== false && $size > 0) ? (string)$size : '-';

        // Escape double-quotes inside variable fields (RFC 7230 style)
        $safeReferrer  = str_replace('"', '\\"', $referrer);
        $safeUserAgent = str_replace('"', '\\"', $userAgent);
        $safeRequest   = $method . ' ' . $uri . ' ' . $proto;

        $line = sprintf(
            '%s - - [%s] "%s" %d %s "%s" "%s"' . "\n",
            $ip,
            $timestamp,
            $safeRequest,
            $status,
            $size,
            $safeReferrer,
            $safeUserAgent
        );

        file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
    });
})();
