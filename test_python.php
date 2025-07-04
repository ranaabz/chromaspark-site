<?php
// Diagnostic script to check Python access from PHP
$python = "python"; // or "python3" depending on your system
$cmd = "$python --version 2>&1";  // capture stdout and stderr
$output = shell_exec($cmd);

echo "<h2>✅ Python Version Output</h2><pre>" . htmlspecialchars($output) . "</pre>";

$cvTest = "$python -c \"import cv2; print('✅ OpenCV version:', cv2.__version__)\" 2>&1";
$cvOutput = shell_exec($cvTest);
echo "<h2>🧪 OpenCV Import Test</h2><pre>" . htmlspecialchars($cvOutput) . "</pre>";
?>
