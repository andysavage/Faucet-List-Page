<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Determine data directory
$dataDir = __DIR__ . '/../../data';
if (!is_dir($dataDir)) {
    // Try sibling data folder as fallback (depending on server structure)
    if (is_dir(__DIR__ . '/../data')) {
        $dataDir = __DIR__ . '/../data';
    }
}

// Determine file path based on type
$type = $_GET['type'] ?? 'banner';
if ($type === 'floating') {
    $file = $dataDir . '/ads-floating.txt';
} else {
    $file = $dataDir . '/ads.txt';
}

// Check if file exists
if (!file_exists($file)) {
    echo json_encode(['success' => true, 'ads' => []]);
    exit;
}

// Read file content
$content = file_get_contents($file);

// Parse ads (split by ---)
$ads = array_filter(
    array_map('trim', explode('---', $content)),
    function($ad) { return !empty($ad); }
);

echo json_encode([
    'success' => true,
    'ads' => array_values($ads)
]);
