<?php
// connect.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/Exception.php';
require 'phpmailer/PHPMailer.php';
require 'phpmailer/SMTP.php';

// 1. Connect to DB
$conn = new mysqli("localhost", "root", "", "chroma_spark");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['email']) && isset($_POST['password'])) {
        // 3. Get form data
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

        // 4. Generate a verification token
        $token = bin2hex(random_bytes(16)); // Token generation
        $is_verified = 0; // Account initially not verified

        // 5. Insert the new user into the database
        $sql = "INSERT INTO clients (username, email, password, verification_token, is_verified) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $username, $email, $password, $token, $is_verified);

        if ($stmt->execute()) {
            // 6. Send verification email
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp-relay.brevo.com'; // Sendinblue SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = '8a879f001@smtp-brevo.com'; // Your Sendinblue SMTP username
                $mail->Password = '4hjGpDFY6anSXUwm'; // Your Sendinblue SMTP password (API key)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Sender and recipient
                $mail->setFrom('8a879f001@smtp-brevo.com', 'Chroma Spark');
                $mail->addAddress($email, $username); // Client's email

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Email Verification - Chroma Spark';
                $verification_link = "http://ChromaSpark.com/verify.php?email=$email&token=$token";
                $mail->Body = "Hi $username,<br><br>Please click the link below to verify your account:<br><a href='$verification_link'>$verification_link</a><br><br>Thanks,<br>Chroma Spark";

                $mail->send();
                echo "Signup successful! Please check your email to verify your account.";
            } catch (Exception $e) {
                echo "Signup successful, but email could not be sent. Error: {$mail->ErrorInfo}";
            }
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: Missing required fields!";
    }
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
