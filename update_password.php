<?php
include 'AdminPanel.php'; // Ensure $conn is defined here

// Handle password update
if (isset($_POST['token']) && isset($_POST['new_password'])) {
    $token = $_POST['token'];
    $new_password = $_POST['new_password'];

    // Check password strength
    if (strlen($new_password) < 6) {
        header("Location: reset_password.php?token=" . urlencode($token) . "&error=Password must be at least 6 characters.");
        exit();
    }

    // Get email using the token and check expiry
    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $expires_at = strtotime($row['expires_at']);
        $current_time = time();

        // Check if token is expired
        if ($current_time > $expires_at) {
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=Token has expired.");
            exit();
        }

        // Hash the password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password in clients table
        $update_stmt = $conn->prepare("UPDATE clients SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $hashed_password, $email);
        $update_stmt->execute();

        if ($update_stmt->affected_rows === 0) {
            header("Location: reset_password.php?token=" . urlencode($token) . "&error=Password update failed.");
            exit();
        }

        // Delete token from password_resets table
        $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $delete_stmt->bind_param("s", $token);
        $delete_stmt->execute();

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
