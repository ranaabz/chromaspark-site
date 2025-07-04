<?php
include 'AdminPanel.php'; // Include database connection

// Ensure that the id is an integer (sanitize input)
$id = intval($_POST['id']); 

// Use prepared statement to prevent SQL injection
$sql = "DELETE FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);  // 'i' indicates the type is an integer

if ($stmt->execute()) {
    echo "Project deleted successfully!";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
?>
