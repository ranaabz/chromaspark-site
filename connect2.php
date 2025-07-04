<?php
session_start();
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';
require __DIR__ . '/phpmailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $con = new mysqli('localhost', 'root', '', 'chroma_spark');

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Include is_deleted check in the query
    $sql = "SELECT * FROM clients WHERE username = ? AND is_deleted = 0";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            // ✅ Generate 6-digit 2FA code
            $code = rand(100000, 999999);
            $_SESSION['2fa_code'] = $code;
            $_SESSION['2fa_expires'] = time() + 300; // 5 minutes
            $_SESSION['client_id_temp'] = $user['id']; // store temporarily until 2FA is verified
            $_SESSION['username_temp'] = $user['username'];
            $_SESSION['email_temp'] = $user['email'];

            // ✅ Send code via email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'chromaspark0@gmail.com';
                $mail->Password = 'ssiz jqwh ugze puqy';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark');
                $mail->addAddress($user['email'], $user['username']);

                $mail->isHTML(true);
                $mail->Subject = 'Your 2FA Code';
                $mail->Body = "<p>Your Chroma Spark 2FA code is: <strong>$code</strong></p><p>This code will expire in 5 minutes.</p>";

                $mail->send();

                // ✅ Redirect to 2FA verification page
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
        // The user might exist but is deleted
        $checkDeleted = $con->prepare("SELECT id FROM clients WHERE username = ? AND is_deleted = 1");
        $checkDeleted->bind_param("s", $username);
        $checkDeleted->execute();
        $deletedResult = $checkDeleted->get_result();

        if ($deletedResult->num_rows > 0) {
            header("Location: login.php?error=account_deleted");
        } else {
            header("Location: login.php?error=user_not_found");
        }
        exit();
    }
}
?>
