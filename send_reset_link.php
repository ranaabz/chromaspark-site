<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php'; 

// Database connection
$conn = new mysqli("localhost", "root", "", "chroma_spark");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get email from POST
$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: forget_password.php?error=Invalid email format.");
    exit();
}

// Check if email exists in the clients table
$stmt = $conn->prepare("SELECT id FROM clients WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    header("Location: forget_password.php?error=No account found with that email.");
    exit();
}

// Generate reset token and expiration
$token = bin2hex(random_bytes(32));
$expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

// Save token to password_resets table
$stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?) 
    ON DUPLICATE KEY UPDATE token = VALUES(token), expires_at = VALUES(expires_at)");
$stmt->bind_param("sss", $email, $token, $expires);
$stmt->execute();

// Create reset link
$reset_link = "http://localhost/SeniorProject/reset_password.php?token=" . urlencode($token);


// Send email with PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP config
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // or your SMTP host
    $mail->SMTPAuth   = true;
    $mail->Username   = 'chromaspark0@gmail.com'; // replace with your Gmail
    $mail->Password   = 'ssiz jqwh ugze puqy';   // use app-specific password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Email settings
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
