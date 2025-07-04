<?php
header("Content-Type: application/json"); // Ensure correct response type

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chroma_spark";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["success" => false, "error" => "Database Connection Failed"]));
}

// Get the JSON input
$data = json_decode(file_get_contents("php://input"));

// Validate if data exists
if (!$data || !isset($data->client_name) || !isset($data->project_name) || !isset($data->rating) || !isset($data->feedback)) {
    echo json_encode(["success" => false, "error" => "Invalid data provided"]);
    exit();
}

// Retrieve sanitized feedback data
$client_name = $conn->real_escape_string($data->client_name);
$project_name = $conn->real_escape_string($data->project_name);
$rating = (int) $data->rating;
$feedback = $conn->real_escape_string($data->feedback);


// Insert the feedback into the feedback table
$sql = "INSERT INTO feedback (client_name, project_name, rating, feedback) 
        VALUES ('$client_name', '$project_name', $rating, '$feedback')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $conn->error]);
}

// Close the connection
$conn->close();
?>
