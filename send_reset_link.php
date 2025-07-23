<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; 

include 'connection.php'; // Your pg_connect in this file, defines $dbconn

// Get email from POST
$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: forget_password.php?error=Invalid email format.");
    exit();
}

// Check if email exists in clients table
$sql = "SELECT id FROM clients WHERE email = $1";
$result = pg_query_params($dbconn, $sql, [$email]);

if (pg_num_rows($result) === 0) {
    header("Location: forget_password.php?error=No account found with that email.");
    exit();
}

// Generate reset token and expiration
$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

// Upsert token into password_resets table
// PostgreSQL UPSERT syntax (ON CONFLICT DO UPDATE)
$sql = "
    INSERT INTO password_resets (email, token, expires_at)
    VALUES ($1, $2, $3)
    ON CONFLICT (email) DO UPDATE
    SET token = EXCLUDED.token,
        expires_at = EXCLUDED.expires_at
";
pg_query_params($dbconn, $sql, [$email, $token, $expires]);

// Change localhost to your production domain
$reset_link = "https://chromaspark.onrender.com/reset_password.php?token=" . urlencode($token);

// Send email with PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'chromaspark0@gmail.com';
    $mail->Password   = 'ssiz jqwh ugze puqy';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Chroma Spark Password Reset';
    $mail->Body    = "
        <h3>Password Reset Request</h3>
        Click <a href='$reset_link'>here</a> to reset your password. This link expires in 1 hour.
    ";

    $mail->send();

    header("Location: forget_password.php?success=Reset link sent. Check your email.");
    exit();
} catch (Exception $e) {
    header("Location: forget_password.php?error=Failed to send email. Please try again.");
    exit();
}
?>
