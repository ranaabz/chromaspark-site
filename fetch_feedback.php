<?php
require 'connection.php'; // Should define $dbconn using pg_connect()

header('Content-Type: application/json');

// Ensure the database connection is available
if (!$dbconn) {
    echo json_encode(["error" => "❌ Database connection failed."]);
    exit;
}

// Query feedback from database
$sql = "SELECT client_name, project_name, rating, feedback, created_at
        FROM feedback
        ORDER BY created_at DESC";

$result = pg_query($dbconn, $sql);

if (!$result) {
    echo json_encode(["error" => "❌ SQL Error: " . pg_last_error($dbconn)]);
    exit;
}

// Collect results
$feedbackList = [];
while ($row = pg_fetch_assoc($result)) {
    $row['rating_stars'] = str_repeat('★', intval($row['rating']));
    $feedbackList[] = $row;
}

// Return data
if (!empty($feedbackList)) {
    echo json_encode($feedbackList);
} else {
    echo json_encode(["message" => "ℹ️ No feedback available."]);
}

// Close the connection
pg_close($dbconn);
?>
