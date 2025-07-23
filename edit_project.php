<?php
include 'connection.php'; // Should set $dbconn for PostgreSQL connection

$id = intval($_POST['id']);
$name = $_POST['name'];
$description = $_POST['description'];

// Use parameterized query to avoid SQL injection
$sql = "UPDATE projects SET name = $1, description = $2 WHERE id = $3";

$result = pg_query_params($dbconn, $sql, array($name, $description, $id));

if ($result) {
    echo "Project updated successfully!";
} else {
    echo "Error: " . pg_last_error($dbconn);
}
?>
