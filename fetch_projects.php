<?php
require "connection.php"; 

header('Content-Type: application/json');

$sql = "SELECT id, name, description, image_url FROM projects";
$result = pg_query($dbconn, $sql);

if (!$result) {
    echo json_encode(["error" => "SQL Error: " . pg_last_error($dbconn)]);
    exit;
}

$projects = [];
while ($row = pg_fetch_assoc($result)) {
    $projects[] = $row;
}

echo json_encode($projects);

pg_close($dbconn);
?>
