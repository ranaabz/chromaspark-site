<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Connect to PostgreSQL - update with your Render DB credentials or env vars
$dbconn = pg_connect("host=dpg-d209sfre5dus73d5rfsg-a dbname=chroma_spark_nfki user=chroma_spark_nfki_user password=MJfUXNzs4727vpq98rdbLtiPRDVTrILE port=5432");
if (!$dbconn) {
    die("Connection failed: " . pg_last_error());
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
    $is_verified = false; // boolean in PostgreSQL

    // Insert user into DB
    $sql = "INSERT INTO clients (username, email, password, verification_token, is_verified) 
            VALUES ($1, $2, $3, $4, $5)";
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
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'chromaspark0@gmail.com';
            $mail->Password = 'ssiz jqwh ugze puqy';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark');
            $mail->addAddress($email, $username);

            $mail->isHTML(true);
            $mail->Subject = 'Verify Your Email Address';

            // Change localhost to your production domain!
            $verification_link = 'https://chromaspark.onrender.com/verify.php?token=' . $verification_token . '&email=' . urlencode($email);

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

            header("Location: check_email.html");
            exit();
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "Error inserting user: " . pg_last_error($dbconn);
    }

    pg_close($dbconn);
}
?>
