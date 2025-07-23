<?php
include 'connection.php'; // This should define $dbconn via pg_connect

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get project ID from POST data
    $project_id = $_POST['project_id'] ?? null;

    if (!$project_id || !is_numeric($project_id)) {
        echo "Invalid project ID.";
        exit;
    }

    // Increment view count safely using pg_query_params
    $sql = "UPDATE projects SET views = views + 1 WHERE id = $1";
    $result = pg_query_params($dbconn, $sql, [$project_id]);

    if ($result) {
        echo "View count updated successfully!";
    } else {
        echo "Error updating view count: " . pg_last_error($dbconn);
    }
}
?>
