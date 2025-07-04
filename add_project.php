<?php
include 'AdminPanel.php';

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

    // Use prepared statements to insert data into the database
    $stmt = $conn->prepare("INSERT INTO projects (name, description, image_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $description, $imagePath);  // "sss" means three strings
    
    if ($stmt->execute()) {
        echo "Project added successfully!";
    } else {
        echo "Error: " . $stmt->error; // Show specific error if any
    }

    $stmt->close();  // Close the prepared statement
}
?>
