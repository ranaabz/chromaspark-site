<?php
session_start();
<?php
session_start();

// ✅ Load PHPMailer classes using absolute path
require __DIR__ . '/phpmailer/PHPMailer.php';
require __DIR__ . '/phpmailer/SMTP.php';
require __DIR__ . '/phpmailer/Exception.php';

// ✅ Use the correct namespaces
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$allowed_users = [
    'Rana' => ['password' => 'collaboration', 'email' => 'rana.abz92@gmail.com'],
    'Yara' => ['password' => '2025success', 'email' => 'abouzouryara@gmail.com'],
    'Soriana' => ['password' => 'p@ss!on', 'email' => 'soriana@example.com']
];

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (array_key_exists($username, $allowed_users)) {
        if ($allowed_users[$username]['password'] === $password) {
            $email = $allowed_users[$username]['email'];
            $verification_code = rand(100000, 999999);
            $_SESSION['username'] = $username;
            $_SESSION['verification_code'] = $verification_code;

            // Send email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'chromaspark0@gmail.com'; // your Gmail address
                $mail->Password = 'ssiz jqwh ugze puqy';     // your Gmail app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark Admin Verification');
                $mail->addAddress($email);
                $mail->Subject = 'Your Admin Verification Code';
                $mail->Body = "Hello $username,\n\nYour verification code is: $verification_code";

                $mail->send();
                header('Location: admin_verify.php');
                exit();
            } catch (Exception $e) {
                $error_message = 'Email sending failed. Please try again.';
            }
        } else {
            $error_message = 'Invalid credentials!';
        }
    } else {
        $error_message = 'Invalid credentials!';
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chroma Spark Admin Login</title>
    <link rel="icon" type="image/png" href="Chromaspark.png.jpeg">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <form method="POST" id="loginForm">
            <input type="text" name="username" id="username" placeholder="Username" required>
            <input type="password" name="password" id="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <?php if ($error_message): ?>
                <p id="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>
</body>
</html>

<style>
    body {
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: url('Chromaspark.png.jpeg') no-repeat center center/cover;
        font-family: Arial, sans-serif;
    }

    .login-container {
        background: rgba(0, 0, 0, 0.7);
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        color: white;
        width: 300px;
        margin: 10px;
    }

    input {
        width: 100%;
        padding: 10px;
        margin: 10px 0;
        border: none;
        border-radius: 5px;
    }

    button {
        width: 100%;
        padding: 10px;
        background-color: #ff9800;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    #error-message {
        color: red;
        font-size: 14px;
    }
</style>
