<?php
header("Content-Type: application/json");

include 'connection.php'; // defines $dbconn with pg_connect

// Get JSON input
$data = json_decode(file_get_contents("php://input"));

if (!$data || 
    !isset($data->client_name) || 
    !isset($data->project_name) || 
    !isset($data->rating) || 
    !isset($data->feedback)) {
    echo json_encode(["success" => false, "error" => "Invalid data provided"]);
    exit();
}

$client_name = trim($data->client_name);
$project_name = trim($data->project_name);
$rating = intval($data->rating);
$feedback = trim($data->feedback);

// Basic validation can be added here

$sql = "INSERT INTO feedback (client_name, project_name, rating, feedback, created_at, updated_at)
        VALUES ($1, $2, $3, $4, NOW(), NOW())";

$params = [$client_name, $project_name, $rating, $feedback];

$result = pg_query_params($dbconn, $sql, $params);

if ($result) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => pg_last_error($dbconn)]);
}
?>
