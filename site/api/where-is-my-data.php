<?php
/**
 * FaucetList Storage Path Diagnostic
 */
header('Content-Type: text/plain');

$targetDir = __DIR__ . '/../../data/faucetlist';
$realPath = realpath($targetDir);

echo "--- FaucetList Path Diagnostic ---\n\n";
echo "Script Location: " . __FILE__ . "\n";
echo "Configured Target: " . $targetDir . "\n";
echo "Resolved Real Path: " . ($realPath ?: "PATH DOES NOT EXIST OR IS INACCESSIBLE") . "\n\n";

if ($realPath && is_dir($realPath)) {
    echo "Files found in this folder:\n";
    $files = scandir($realPath);
    $found = false;
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            echo " - $file (" . filesize($realPath . '/' . $file) . " bytes)\n";
            $found = true;
        }
    }
    if (!$found) echo " (Directory is physically empty)\n";
} else {
    echo "CRITICAL: The directory does not exist or PHP cannot see it.\n";
    echo "Parent folder writable: " . (is_writable(dirname($targetDir)) ? 'Yes' : 'No') . "\n";
}

echo "\n--- End of Diagnostic ---\n";
?>
