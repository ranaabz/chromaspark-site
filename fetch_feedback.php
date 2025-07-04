<?php
require 'AdminPanel.php'; // Ensure this connects to your MySQL database

header('Content-Type: application/json');

// Ensure the database connection is established
if (!$conn) {
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

// Fetch feedback with project and client names
$sql = "SELECT f.client_name, f.project_name, f.rating, f.feedback, f.created_at 
        FROM feedback f 
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    // Print MySQL error message
    echo json_encode(["error" => "SQL Error: " . $conn->error, "sql" => $sql]);
    exit;
}

if ($result->num_rows > 0) {
    $feedbackList = [];
    while ($row = $result->fetch_assoc()) {
        // Optionally, convert rating to a number of stars for easier front-end rendering
        $row['rating_stars'] = str_repeat('★', $row['rating']);
        $feedbackList[] = $row;
    }
    echo json_encode($feedbackList);
} else {
    echo json_encode(["error" => "No feedback available."]);
}

// Close the connection
$conn->close();
?>
