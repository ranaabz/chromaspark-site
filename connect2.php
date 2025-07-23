<?php
session_start();
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';
require __DIR__ . '/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

include 'connection.php';  // Your centralized Postgres connection as $dbconn

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Prepare SQL to fetch user where not deleted
    $sql = "SELECT * FROM clients WHERE username = $1 AND is_deleted = FALSE";
    $result = pg_query_params($dbconn, $sql, [$username]);

    if (!$result) {
        die("Database error: " . pg_last_error($dbconn));
    }

    $user = pg_fetch_assoc($result);

    if ($user) {
        // Verify password (assuming passwords are hashed with password_hash)
        if (password_verify($password, $user['password'])) {
            // Generate 6-digit 2FA code
            $code = rand(100000, 999999);
            $_SESSION['2fa_code'] = $code;
            $_SESSION['2fa_expires'] = time() + 300; // 5 minutes expiration
            $_SESSION['client_id_temp'] = $user['id'];
            $_SESSION['username_temp'] = $user['username'];
            $_SESSION['email_temp'] = $user['email'];

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'chromaspark0@gmail.com';
                $mail->Password = 'ssiz jqwh ugze puqy';  // Use app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark');
                $mail->addAddress($user['email'], $user['username']);

                $mail->isHTML(true);
                $mail->Subject = 'Your 2FA Code';
                $mail->Body = "<p>Your Chroma Spark 2FA code is: <strong>$code</strong></p><p>This code will expire in 5 minutes.</p>";

                $mail->send();

                header("Location: verify_2fa.php");
                exit();
            } catch (Exception $e) {
                die("2FA code could not be sent. Mailer Error: {$mail->ErrorInfo}");
            }
        } else {
            header("Location: login.php?error=wrong_password");
            exit();
        }
    } else {
        // Check if user is deleted
        $checkDeletedSql = "SELECT id FROM clients WHERE username = $1 AND is_deleted = TRUE";
        $deletedResult = pg_query_params($dbconn, $checkDeletedSql, [$username]);

        if ($deletedResult && pg_num_rows($deletedResult) > 0) {
            header("Location: login.php?error=account_deleted");
        } else {
            header("Location: login.php?error=user_not_found");
        }
        exit();
    }
}
?>
