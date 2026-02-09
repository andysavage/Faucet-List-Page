<?php
$faucetDataDir = __DIR__ . '/../../data/faucetlist';
echo "Target Dir: " . $faucetDataDir . "\n";
echo "Real Path: " . realpath($faucetDataDir) . "\n";

if (is_dir($faucetDataDir) || @mkdir($faucetDataDir, 0755, true)) {
    echo "Directory is ready. Attempting to write a test file...\n";
    $testFile = $faucetDataDir . '/test_sync.json';
    if (file_put_contents($testFile, json_encode(['working' => true, 'time' => time()]))) {
        echo "Successfully wrote: " . $testFile . "\n";
    } else {
        echo "Failed to write test file.\n";
    }
    
    echo "Directory contents:\n";
    $files = scandir($faucetDataDir);
    foreach ($files as $file) {
        if ($file != "." && $file != "..") {
            echo " - $file (" . filesize($faucetDataDir . '/' . $file) . " bytes)\n";
        }
    }
} else {
    echo "Failed to access or create directory: " . $faucetDataDir . "\n";
}
?>
