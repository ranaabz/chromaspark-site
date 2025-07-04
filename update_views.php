<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get project ID from POST data
    $project_id = $_POST['project_id'];

    // Database connection
    $conn = new mysqli("localhost", "root", "", "chroma_spark");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Increment view count in the database
    $stmt = $conn->prepare("UPDATE projects SET views = views + 1 WHERE id = ?");
    $stmt->bind_param("i", $project_id);

    if ($stmt->execute()) {
        echo "View count updated successfully!";
    } else {
        echo "Error updating view count: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
