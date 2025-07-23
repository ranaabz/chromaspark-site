<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Logo Analysis</title></head><body style='font-family:sans-serif;'>";

if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $_FILES['logo']['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        echo "❌ Invalid file type. Please upload an image (JPG, PNG, GIF).";
        exit;
    }

    $targetDir = __DIR__ . "/uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $originalName = basename($_FILES["logo"]["name"]);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $safeBaseName = preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($originalName, PATHINFO_FILENAME));
    $safeFilename = $safeBaseName . "." . $ext;
    $targetFile = $targetDir . $safeFilename;

    if (move_uploaded_file($_FILES["logo"]["tmp_name"], $targetFile)) {
        $python = escapeshellcmd("C:/Users/ABM/AppData/Local/Programs/Python/Python313/python.exe");

        $analyzeScript = escapeshellarg(__DIR__ . "/analyze_logo.py");
        $imagePath = escapeshellarg($targetFile);
        $cmd1 = "$python $analyzeScript $imagePath 2>&1";
        $output1 = shell_exec($cmd1);

        if (!empty($output1)) {
            echo "<h2>🧠 Logo Analysis Result</h2><pre style='background:#05013d;padding:10px;border:1px solid #ccc; color:#eee;'>" . htmlspecialchars($output1) . "</pre>";
        } else {
            echo "⚠️ No output from the Python script. Ensure Python and OpenCV are correctly installed.";
        }

        // Error segmentation
        $segmentScript = escapeshellarg(__DIR__ . "/segment_logo_cv.py");
        $cmd2 = "$python $segmentScript $imagePath 2>&1";
        $seg_output = trim(shell_exec($cmd2));

        if (!empty($seg_output) && file_exists($seg_output)) {
            $webPath = str_replace(__DIR__, '', $seg_output);
            echo "<h3>🚩 Error Segmentation Map</h3>";
            echo "<img src='" . htmlspecialchars($webPath) . "' style='max-width:500px;border:2px solid #aaa;'><br><br>";
        } else {
            echo "⚠️ Error segmentation failed or file not found.";
        }

        // Extract annotated image filename from $output1 if available
        if (preg_match('/Annotated Image Saved:\s*(.+\.png)/i', $output1, $matches)) {
            $annotatedFilename = trim($matches[1]);
            $annotatedPath = $targetDir . $annotatedFilename;
            if (file_exists($annotatedPath)) {
                $webAnnotatedPath = str_replace(__DIR__, '', $annotatedPath);
                echo "<h3>📸 Annotated Output</h3>";
                echo "<img src='" . htmlspecialchars($webAnnotatedPath) . "' style='max-width:600px;border:2px solid #000;' />";
            }
        }

    } else {
        echo "❌ Failed to save uploaded file.";
    }
} else {
    $errorMsg = isset($_FILES['logo']['error']) ? $_FILES['logo']['error'] : 'No file uploaded';
    echo "❌ Upload error: " . htmlspecialchars($errorMsg);
}

echo "</body></html>";
?>
