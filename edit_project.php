<?php
include 'AdminPanel.php'; // Include database connection

$id = $_POST['id'];
$name = $_POST['name'];
$description = $_POST['description'];

$sql = "UPDATE projects SET name = '$name', description = '$description' WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Project updated successfully!";
} else {
    echo "Error: " . $conn->error;
}
?>
