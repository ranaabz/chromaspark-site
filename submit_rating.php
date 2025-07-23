<?php
include 'connection.php';  // $dbconn via pg_connect

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Invalid request method.";
    exit();
}

// Get and sanitize inputs (basic sanitization)
$project_id = intval($_POST['project_id']);
$rating = intval($_POST['rating']);
$feedback = trim($_POST['feedback']);
$name = trim($_POST['name']);
$email = trim($_POST['email']);

// You might want to add more validation here (e.g., valid email, rating range, etc.)

// Get project_name from projects table based on project_id
$sql_project = "SELECT name FROM projects WHERE id = $1";
$result_project = pg_query_params($dbconn, $sql_project, [$project_id]);

if (!$result_project || pg_num_rows($result_project) === 0) {
    echo "Invalid project.";
    exit();
}

$project_row = pg_fetch_assoc($result_project);
$project_name = $project_row['name'];

// Store current timestamp
$created_at = date('Y-m-d H:i:s');
$updated_at = $created_at;

// Calculate rating_stars (assuming same as rating, or you can store rating as integer stars)
$rating_stars = $rating;

// Insert into feedback table
$sql_insert = "INSERT INTO feedback 
    (client_name, project_name, rating, rating_stars, feedback, created_at, updated_at) 
    VALUES ($1, $2, $3, $4, $5, $6, $7)";

$params = [$name, $project_name, $rating, $rating_stars, $feedback, $created_at, $updated_at];

$result_insert = pg_query_params($dbconn, $sql_insert, $params);

if ($result_insert) {
    echo "Thank you! Your feedback was submitted.";
} else {
    echo "Error: " . pg_last_error($dbconn);
}
?>
