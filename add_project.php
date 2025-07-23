<?php
include 'connection.php';  // This should define $dbconn via pg_connect

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];

        if ($image['error'] != 0) {
            echo "❌ Error uploading image.";
            exit;
        }

        // Render: Upload to /tmp instead of /uploads
        $uploadDir = '/tmp/';
        $imageFilename = basename($image['name']);
        $imagePath = $uploadDir . $imageFilename;

        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            echo "✅ Image uploaded to temporary storage.<br>";
        } else {
            echo "❌ Failed to move uploaded file.<br>";
            exit;
        }

        // Optionally: move to permanent CDN or log filename for DB
        $dbImagePath = $imageFilename; // Just the name to display later

    } else {
        echo "❌ No image uploaded.";
        exit;
    }

    // Insert into PostgreSQL
    $sql = "INSERT INTO projects (name, description, image_url) VALUES ($1, $2, $3)";
    $params = [$name, $description, $dbImagePath];

    $result = pg_query_params($dbconn, $sql, $params);

    if ($result) {
        echo "✅ Project added successfully!";
    } else {
        echo "❌ DB Error: " . pg_last_error($dbconn);
    }
}
?>
