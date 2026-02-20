<?php
/**
 * Analytics refresh wrapper.
 * Called by the Refresh button in stats.html.
 * Runs analytics.php internally so the token never appears in the browser.
 */

header('Content-Type: application/json');

$token    = getenv('ANALYTICS_TOKEN') ?: 'change-this-secret';
$self_url = 'http://localhost/analytics.php?token=' . urlencode($token);

$ctx = stream_context_create([
    'http' => [
        'timeout' => 30,
        'header'  => 'Host: ' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n",
    ],
]);

$result = @file_get_contents($self_url, false, $ctx);

if ($result === false) {
    // file_get_contents via localhost may be blocked on some hosts.
    // Fall back to including analytics.php directly.
    ob_start();
    $_GET['token'] = $token;
    include __DIR__ . '/analytics.php';
    $result = ob_get_clean();
}

echo json_encode([
    'ok'     => true,
    'output' => trim($result),
]);
