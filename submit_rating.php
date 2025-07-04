<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "chroma_spark";

$conn = new mysqli($host, $user, $password, $dbname);

// Check for DB connection error
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get and sanitize input
$project_id = intval($_POST['project_id']);
$rating = intval($_POST['rating']);
$feedback = $conn->real_escape_string($_POST['feedback']);
$name = $conn->real_escape_string($_POST['name']);
$email = $conn->real_escape_string($_POST['email']);

// Insert into database
$sql = "INSERT INTO feedback (client_name,
    project_name,
    rating,
    rating_stars,
    feedback,
    created_at,
    updated_at)
        VALUES (?, ?, ?, ?, ?, ?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisii", $client_name, $rating, $feedback, $project_name, $created_at);

if ($stmt->execute()) {
    echo "Thank you! Your feedback was submitted.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
