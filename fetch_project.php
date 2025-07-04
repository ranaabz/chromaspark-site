<?php
include 'AdminPanel.php';

$sql = "SELECT * FROM projects ORDER BY created_at DESC";
$result = $conn->query($sql);

$projects = [];
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

echo json_encode($projects);
?>
