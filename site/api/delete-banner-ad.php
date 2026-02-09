<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'auth-helper.php';

// Require admin privileges
requireAdmin();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['index'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$index = intval($input['index']);

// Determine data directory
$dataDir = __DIR__ . '/../../data';
if (!is_dir($dataDir)) {
    if (is_dir(__DIR__ . '/../data')) {
        $dataDir = __DIR__ . '/../data';
    }
}

// Determine file path
$type = $input['type'] ?? 'banner';
if ($type === 'floating') {
    $file = $dataDir . '/ads-floating.txt';
} else {
    $file = $dataDir . '/ads.txt';
}

// Check if file exists
if (!file_exists($file)) {
    echo json_encode(['success' => false, 'error' => 'File not found']);
    exit;
}

// Read and parse ads
$content = file_get_contents($file);
$ads = array_filter(
    array_map('trim', explode('---', $content)),
    function($ad) { return !empty($ad); }
);
$ads = array_values($ads); // Re-index

// Validate index
if ($index < 0 || $index >= count($ads)) {
    echo json_encode(['success' => false, 'error' => 'Invalid index']);
    exit;
}

// Remove the ad at the specified index
array_splice($ads, $index, 1);

// Rebuild content with separators
$newContent = implode("\n---\n", $ads);
if (!empty($newContent)) {
    $newContent .= "\n";
}

// Write to file
if (file_put_contents($file, $newContent) === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to write file']);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Banner ad deleted successfully']);
