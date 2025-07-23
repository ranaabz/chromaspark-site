<?php
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];

        if ($image['error'] !== UPLOAD_ERR_OK) {
            echo "❌ Error uploading image.";
            exit;
        }

        $uploadDir = '/mnt/persistent/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageFilename = preg_replace("/[^a-zA-Z0-9_\.-]/", "_", basename($image['name']));
        $imagePath = $uploadDir . $imageFilename;

        if (!move_uploaded_file($image['tmp_name'], $imagePath)) {
            echo "❌ Failed to move uploaded file.";
            exit;
        }

        $dbImagePath = 'uploads/' . $imageFilename;
    } else {
        echo "❌ No image uploaded.";
        exit;
    }

    $sql = "INSERT INTO projects (name, description, image_url) VALUES ($1, $2, $3)";
    $result = pg_query_params($dbconn, $sql, [$name, $description, $dbImagePath]);

    if ($result) {
        echo "✅ Project added successfully!";
    } else {
        echo "❌ Database error: " . pg_last_error($dbconn);
    }
}
?>
