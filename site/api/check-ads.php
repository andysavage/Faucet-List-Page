<?php
header('Content-Type: text/plain');

$dataDir = __DIR__ . '/../../data';
if (!is_dir($dataDir) && is_dir(__DIR__ . '/../data')) {
    $dataDir = __DIR__ . '/../data';
}

echo "Detected Data Dir: " . $dataDir . "\n";
echo "Dir Writable: " . (is_writable($dataDir) ? "Yes" : "No") . "\n\n";

$files = ['ads.txt', 'ads-floating.txt'];
foreach ($files as $filename) {
    $file = $dataDir . '/' . $filename;
    echo "--- $filename ---\n";
    if (file_exists($file)) {
        echo file_get_contents($file);
    } else {
        echo "File does not exist at: " . $file;
    }
    echo "\n\n";
}
