<?php
include 'connection.php';  // Your PostgreSQL connection file with $dbconn = pg_connect(...);
require 'vendor/autoload.php';  // PHPMailer autoload

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$email || !$password) {
        die("All fields are required.");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate verification token
    $verification_token = bin2hex(random_bytes(16));
    $is_verified = false;

    // Insert user using pg_query_params to avoid injection
    $sql = "INSERT INTO clients (username, email, password, verification_token, is_verified) VALUES ($1, $2, $3, $4, $5)";
    $result = pg_query_params($dbconn, $sql, [
        $username,
        $email,
        $hashed_password,
        $verification_token,
        $is_verified
    ]);

    if ($result) {
        // Send verification email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'chromaspark0@gmail.com';
            $mail->Password   = 'ssiz jqwh ugze puqy'; // Your app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark');
            $mail->addAddress($email, $username);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email Address';

            // Change domain to your production URL
            $verification_link = 'https://chromaspark.onrender.com/verify.php?token=' . $verification_token . '&email=' . urlencode($email);

            $mail->Body = "
                <html>
                <body>
                    <h2>Welcome to Chroma Spark!</h2>
                    <p>Thanks for signing up. Please verify your email by clicking the link below:</p>
                    <a href='$verification_link'>Verify Email</a>
                </body>
                </html>
            ";

            $mail->AltBody = "Thanks for signing up. Verify your email here: $verification_link";

            $mail->send();

            header('Location: check_email.php');
            exit();
        } catch (Exception $e) {
            echo "Verification email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error saving user: " . pg_last_error($dbconn);
    }

    pg_close($dbconn);
} else {
    die("Invalid request method.");
}
