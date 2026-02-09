<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

require_once 'auth-helper.php';

// Require admin privileges
requireAdmin();

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['html'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$html = trim($input['html']);

// Validate HTML is not empty
if (empty($html)) {
    echo json_encode(['success' => false, 'error' => 'HTML cannot be empty']);
    exit;
}

// Determine data directory
$dataDir = __DIR__ . '/../../data';
if (!is_dir($dataDir)) {
    if (is_dir(__DIR__ . '/../data')) {
        $dataDir = __DIR__ . '/../data';
    } else {
        // Create it if it doesn't exist
        mkdir(__DIR__ . '/../../data', 0755, true);
        $dataDir = __DIR__ . '/../../data';
    }
}

// Determine file path
$type = $input['type'] ?? 'banner';
if ($type === 'floating') {
    $file = $dataDir . '/ads-floating.txt';
} else {
    $file = $dataDir . '/ads.txt';
}

// Read existing content and parse ads
$existingContent = file_exists($file) ? file_get_contents($file) : '';
$ads = array_filter(array_map('trim', explode('---', $existingContent)));

// Check if we're updating an existing ad (index provided) or adding new
$editIndex = isset($input['index']) ? (int)$input['index'] : -1;

if ($editIndex >= 0 && $editIndex < count($ads)) {
    // Update existing ad
    $ads[$editIndex] = $html;
    $message = 'Banner ad updated successfully';
} else {
    // Append new ad
    $ads[] = $html;
    $message = 'Banner ad added successfully';
}

// Rebuild file content
$newContent = implode("\n---\n", $ads) . "\n";

// Write to file
if (file_put_contents($file, $newContent) === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to write file']);
    exit;
}

echo json_encode(['success' => true, 'message' => $message]);
