<?php
include 'connection.php'; // This file should establish a PostgreSQL connection and store in $dbconn

// Sanitize input
$id = intval($_POST['id']); 

// Prepare and execute delete query
$sql = "DELETE FROM projects WHERE id = $1";
$result = pg_query_params($dbconn, $sql, array($id));

if ($result) {
    echo "Project deleted successfully!";
} else {
    echo "Error: " . pg_last_error($dbconn);
}
?>
