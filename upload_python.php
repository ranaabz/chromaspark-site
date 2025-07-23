<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDir = __DIR__ . '/uploads/';  // Use absolute path for safety

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $uploadedFile = $uploadDir . basename($_FILES['logo']['name']);

    // Move uploaded file
    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadedFile)) {
        // Sanitize the file path for shell command
        $escapedFile = escapeshellarg($uploadedFile);

        // Command to run Python script
        $command = "python3 analyze_logo.py $escapedFile 2>&1"; // Capture stderr as well
        $output = shell_exec($command);

        echo "<pre>" . htmlspecialchars($output) . "</pre>";

        // Check for generated files with absolute paths
        $reportPdf = $uploadDir . "logo_report.pdf";
        $debugPng = $uploadDir . "debug_output.png";

        echo '<div style="margin-top:20px;">';
        if (file_exists($reportPdf)) {
            // Use relative URL path for links, assuming 'uploads/' is web-accessible
            echo '<a href="uploads/logo_report.pdf" download class="download-btn">📥 Download Logo Report (PDF)</a> ';
        }
        if (file_exists($debugPng)) {
            echo '<a href="uploads/debug_output.png" download class="download-btn">🖼️ Download Annotated PNG</a>';
        }
        echo '</div>';
    } else {
        echo "<p style='color:red;'>❌ Failed to upload file.</p>";
    }
}
?>

<form method="post" enctype="multipart/form-data" style="margin-top:20px;">
    <label for="logo">Upload your logo:</label><br>
    <input type="file" name="logo" id="logo" accept="image/*" required><br><br>
    <button type="submit">Analyze Logo</button>
</form>

<style>
.download-btn {
    display: inline-block;
    margin-top: 10px;
    margin-right: 15px;
    padding: 10px 18px;
    background-color: #1d72b8;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 14px;
}
.download-btn:hover {
    background-color: #155d8b;
}
</style>
