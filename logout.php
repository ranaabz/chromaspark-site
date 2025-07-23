<?php
session_start();

// Include database connection
require_once 'AdminPanel.php'; // Make sure this file creates $conn (PostgreSQL connection resource)

if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Soft delete: mark user as deleted
    $sql = "UPDATE clients SET is_deleted = 1 WHERE id = $1";
    $result = pg_query_params($conn, $sql, array($userId));

    if (!$result) {
        // Optional: handle query error
        error_log("Failed to mark user as deleted: " . pg_last_error($conn));
    }
}

// Clear all session data and destroy session
session_unset();
session_destroy();

// Prevent browser caching of this page
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page after logout
header("Location: our work.html");
exit();
?>
