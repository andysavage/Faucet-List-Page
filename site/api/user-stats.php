<?php
/**
 * User Stats Endpoint
 * Returns aggregate stats from the faucetlist user data directory.
 * Requires admin privileges.
 *
 * GET ?user_id=xxx
 */

header('Content-Type: application/json');

require_once __DIR__ . '/auth-helper.php';
requireAdmin();

$faucetDataDir = __DIR__ . '/../../data/faucetlist';

if (!is_dir($faucetDataDir)) {
    echo json_encode(['success' => true, 'stats' => [
        'total_users'        => 0,
        'active_7d'          => 0,
        'active_30d'         => 0,
        'total_faucets'      => 0,
        'avg_faucets_per_user' => 0,
        'users_with_faucets' => 0,
    ]]);
    exit;
}

$files      = glob($faucetDataDir . '/*.json');
$now        = time();
$totalUsers = 0;
$active7d   = 0;
$active30d  = 0;
$totalFaucets     = 0;
$usersWithFaucets = 0;

foreach ($files as $file) {
    $totalUsers++;
    $mtime = filemtime($file);
    $ageDays = ($now - $mtime) / 86400;

    if ($ageDays <= 7)  $active7d++;
    if ($ageDays <= 30) $active30d++;

    $data = json_decode(file_get_contents($file), true);
    if (is_array($data) && count($data) > 0) {
        $usersWithFaucets++;
        $totalFaucets += count($data);
    }
}

$avg = $usersWithFaucets > 0 ? round($totalFaucets / $usersWithFaucets, 1) : 0;

echo json_encode(['success' => true, 'stats' => [
    'total_users'          => $totalUsers,
    'active_7d'            => $active7d,
    'active_30d'           => $active30d,
    'total_faucets'        => $totalFaucets,
    'avg_faucets_per_user' => $avg,
    'users_with_faucets'   => $usersWithFaucets,
]]);
