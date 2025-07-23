<?php
require 'connection.php'; // This should set up $dbconn for PostgreSQL

header('Content-Type: application/json');

// Check connection
if (!$dbconn) {
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

$sql = "SELECT client_name, project_name, rating, feedback, created_at
        FROM feedback
        ORDER BY created_at DESC";

$result = pg_query($dbconn, $sql);

if (!$result) {
    echo json_encode(["error" => "SQL Error: " . pg_last_error($dbconn)]);
    exit;
}

$feedbackList = [];
while ($row = pg_fetch_assoc($result)) {
    // Add stars string for front-end rendering
    $row['rating_stars'] = str_repeat('★', intval($row['rating']));
    $feedbackList[] = $row;
}

if (count($feedbackList) > 0) {
    echo json_encode($feedbackList);
} else {
    echo json_encode(["error" => "No feedback available."]);
}

pg_close($dbconn);
?>
