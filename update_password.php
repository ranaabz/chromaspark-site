<?php
include 'connection.php';  // This should define $dbconn via pg_connect

// Handle password update
if (isset($_POST['token']) && isset($_POST['new_password'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    // Check password strength
    if (strlen($new_password) < 6) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=Password must be at least 6 characters.");
        exit();
    }

    // Get email and expiry from password_resets table using token
    $sql = "SELECT email, expires_at FROM password_resets WHERE token = $1";
    $result = pg_query_params($dbconn, $sql, [$token]);

    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $email = $row['email'];
        $expires_at = strtotime($row['expires_at']);
        $current_time = time();

        // Check if token is expired
        if ($current_time > $expires_at) {
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=Token has expired.");
            exit();
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in clients table
        $update_sql = "UPDATE clients SET password = $1 WHERE email = $2";
        $update_result = pg_query_params($dbconn, $update_sql, [$hashed_password, $email]);

        if (!$update_result || pg_affected_rows($update_result) === 0) {
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=Password update failed.");
            exit();
        }

        // Delete the token from password_resets table
        $delete_sql = "DELETE FROM password_resets WHERE token = $1";
        pg_query_params($dbconn, $delete_sql, [$token]);

        // Success
        header("Location: login.php?success=Password updated successfully.");
        exit();
    } else {
        // Invalid token
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=Invalid or expired token.");
        exit();
    }
} else {
    header("Location: reset_password.php?error=Invalid request.");
    exit();
}
?>
