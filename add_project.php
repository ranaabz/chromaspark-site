<?php
include 'connection.php';  // Make sure AdminPanel.php has your pg_connect() connection as $dbconn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    // Check if file is uploaded
    if (isset($_FILES['image'])) {
        $image = $_FILES['image'];

        // Check for file upload errors
        if ($image['error'] != 0) {
            echo "Error uploading image.";
            exit;
        }

        // Set the file path for the uploaded image
        $imagePath = 'uploads/' . basename($image['name']);

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($image['tmp_name'], $imagePath)) {
            echo "Image uploaded successfully.";
        } else {
            echo "Failed to upload image.";
            exit;
        }
    } else {
        echo "No image uploaded.";
        exit;
    }

    // Insert data into the database using pg_query_params to avoid SQL injection
    $sql = "INSERT INTO projects (name, description, image_url) VALUES ($1, $2, $3)";
    $params = array($name, $description, $imagePath);

    $result = pg_query_params($dbconn, $sql, $params);

    if ($result) {
        echo "Project added successfully!";
    } else {
        echo "Error: " . pg_last_error($dbconn);
    }
}
?>
