<?php
include 'AdminPanel.php';  // Include the database connection
require 'vendor/autoload.php';  // Include PHPMailer (you need to install PHPMailer with Composer)

// Get form data
$username = $_POST['username'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Hash the password

// Generate a verification token
$verification_token = bin2hex(random_bytes(16));

$stmt = $conn->prepare($sql);

// Insert user data into the database
$sql = "INSERT INTO clients (username, email, password, verification_token, is_verified) VALUES ('$username', '$email', '$password', '$verification_token', 0)";
if ($conn->query($sql) === TRUE) {
    // Send verification email
    require 'send_email.php';  // This will include the email sending logic

    // Redirect to a confirmation page
    header("Location: check_email.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
