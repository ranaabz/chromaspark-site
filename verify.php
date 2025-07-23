<?php
// Connect to PostgreSQL - update with your Render DB credentials or environment variables
$dbconn = pg_connect("host=dpg-d209sfre5dus73d5rfsg-a dbname=chroma_spark_nfki user=chroma_spark_nfki_user password=MJfUXNzs4727vpq98rdbLtiPRDVTrILE port=5432");
if (!$dbconn) {
    die("Connection failed: " . pg_last_error());
}

// Default message
$message = "A verification email has been sent to your address. Please click the link in that email to verify your account.";

if (isset($_GET['token']) && isset($_GET['email'])) {
    $verification_token = $_GET['token'];
    $email = $_GET['email'];

    // Prepare and execute select statement safely
    $sql = "SELECT id, is_verified FROM clients WHERE verification_token = $1 AND email = $2";
    $result = pg_query_params($dbconn, $sql, [$verification_token, $email]);

    if ($result && pg_num_rows($result) === 1) {
        $row = pg_fetch_assoc($result);
        $user_id = $row['id'];
        $is_verified = $row['is_verified'];

        if ($is_verified == 't' || $is_verified === true || $is_verified == 1) {
            $message = "✅ Your email is already verified. You can now log in.";
        } else {
            // Update user as verified
            $update_sql = "UPDATE clients SET is_verified = true, verified_at = NOW() WHERE id = $1";
            $update_result = pg_query_params($dbconn, $update_sql, [$user_id]);

            if ($update_result) {
                $message = "✅ Your email has been successfully verified! You can now log in.";
            } else {
                $message = "❌ Failed to update verification: " . pg_last_error($dbconn);
            }
        }
    } else {
        $message = "❌ Invalid or expired verification link.";
    }
} else {
    $message = "❌ Invalid request. Missing token or email.";
}

pg_close($dbconn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Email Verification</title>
    <link rel="icon" type="image/png" href="Chromaspark.png.jpeg" />
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
            <p><?php echo htmlspecialchars($message); ?></p>
        </div>
        <a href="login.php" class="button">Go to Login</a>
    </div>
</body>
</html>
