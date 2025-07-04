<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'vendor/autoload.php';  // Ensure you have PHPMailer installed

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.sendgrid.net';  // SendGrid SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'apikey';  // This is your SendGrid API Key, NOT your email
    $mail->Password = 'your_sendgrid_api_key';  // Your SendGrid API key
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Recipients
    $mail->setFrom('your_email@yourdomain.com', 'Chroma Spark');
    $mail->addAddress($email, $username);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Verify Your Email Address';
    $mail->Body    = "Hi $username, <br><br> Click the link below to verify your email address: <br><br> 
                      <a href='http://yourwebsite.com/verify.php?token=$verification_token'>Verify Email</a>";

    $mail->send();
    echo 'Verification email sent!';
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}
?>
