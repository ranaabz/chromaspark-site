<?php
include 'connection.php'; // Should set $dbconn as PostgreSQL connection resource

header('Content-Type: application/json');

$sql = "SELECT * FROM projects ORDER BY created_at DESC";
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
