<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Database connection details
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chroma_spark";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize input data
function sanitize_input($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate data
    $username = sanitize_input($_POST["username"]);
    $email = sanitize_input($_POST["email"]);
    $password = sanitize_input($_POST["password"]);

    // Basic validation
    if (empty($username)) {
        die("Username is required.");
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }
    if (empty($password)) {
        die("Password is required.");
    }
    if (strlen($password) < 6) {
        die("Password must be at least 6 characters long.");
    }

    // Hash the password securely
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Generate a verification token
    $verification_token = bin2hex(random_bytes(32));
    $is_verified = 0; // Initially unverified

    // Prepare the SQL statement (FIXED with comma and correct values)
    $sql = "INSERT INTO clients (username, email, password, verification_token, is_verified) 
        VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $username, $email, $hashed_password, $verification_token, $is_verified);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Send verification email
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'chromaspark0@gmail.com'; 
            $mail->Password = 'ssiz jqwh ugze puqy'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark');
            $mail->addAddress($email, $username);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email Address';
            $verification_link = 'http://localhost/SeniorProject/verify.php?token=' . $verification_token . '&email=' . urlencode($email);

            $mail->Body = '
                <html>
                <body>
                    <h2>Welcome to Chroma Spark!</h2>
                    <p>Thank you for signing up. Please click the link below to verify your email address:</p>
                    <a href="' . $verification_link . '">Verify Email</a>
                    <p>If you did not create an account, please ignore this email.</p>
                </body>
                </html>
            ';

            $mail->AltBody = 'Thank you for signing up. Please verify your email by visiting: ' . $verification_link;

            $mail->send();
            // Redirect after sending
            header("Location: check_email.html");
            exit();

        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    } else {
        echo "Error inserting user: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
