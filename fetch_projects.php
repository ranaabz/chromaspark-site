<?php
require "AdminPanel.php"; // Ensure your database connection file is correct

$sql = "SELECT id, name, description, image_url FROM projects";
$result = $conn->query($sql);

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode($projects);
?>
