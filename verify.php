<?php
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

// Default message
$message = "A verification email has been sent to your address. Please click the link in that email to verify your account.";

if (isset($_GET['token']) && isset($_GET['email'])) {
    $verification_token = $_GET['token'];
    $email = $_GET['email'];

    // Prepare statement to find user with matching token and email
    $sql = "SELECT id, is_verified FROM clients WHERE verification_token = ? AND email = ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("ss", $verification_token, $email);
    $stmt->execute();
    $stmt->store_result();

    // If a matching user is found
    if ($stmt->num_rows === 1) {
        $stmt->bind_result($user_id, $is_verified);
        $stmt->fetch();
        $stmt->close();

        if ($is_verified == 1) {
            $message = "✅ Your email is already verified. You can now log in.";
        } else {
            // Mark user as verified and set verified_at
            $update_sql = "UPDATE clients SET is_verified = 1, verified_at = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);

            if ($update_stmt) {
                $update_stmt->bind_param("i", $user_id);
                if ($update_stmt->execute()) {
                    $message = "✅ Your email has been successfully verified! You can now log in.";
                } else {
                    $message = "❌ Failed to update verification: " . $update_stmt->error;
                }
                $update_stmt->close();
            } else {
                $message = "❌ Failed to prepare update statement: " . $conn->error;
            }
        }
    } else {
        $message = "❌ Invalid or expired verification link.";
        $stmt->close();
    }
} else {
    $message = "❌ Invalid request. Missing token or email.";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link rel="icon" type="image/png" href="Chromaspark.png.jpeg">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: linear-gradient(115deg, #0d0235, #fdb43b);
            background-repeat: no-repeat;
            background-size: cover;
            background-attachment: fixed;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }

        .container {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            max-width: 400px;
        }

        h2 {
            color: #333;
        }

        p {
            color: black;
            margin-bottom: 20px;
        }

        .message {
            background-color: #e2f3ff;
            border-left: 4px solid #fdb43b;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .button {
            background-color: #0d0235;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
        }

        .button:hover {
            background-color: #fdb43b;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Email Verification</h2>
        <div class="message">
            <p><?php echo $message; ?></p>
        </div>
        <a href="login.php" class="button">Go to Login</a>
    </div>
</body>
</html>
