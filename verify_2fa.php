<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $entered_code = $_POST['code'];

    // Check if the code exists and is still valid
    if (isset($_SESSION['2fa_code']) && isset($_SESSION['2fa_expires'])) {
        if (time() > $_SESSION['2fa_expires']) {
            session_unset();
            session_destroy();
            die("The verification code has expired. Please log in again.");
        }

        if ($entered_code == $_SESSION['2fa_code']) {
            // 2FA success: log the user in
            $_SESSION['client_id'] = $_SESSION['client_id_temp'];
            $_SESSION['username'] = $_SESSION['username_temp'];

            // Clear temporary 2FA session variables
            unset($_SESSION['2fa_code'], $_SESSION['2fa_expires'], $_SESSION['client_id_temp'], $_SESSION['username_temp'], $_SESSION['email_temp']);

            // Redirect to dashboard
            header("Location: Client-dashboard.php");
            exit();
        } else {
            $error = "Invalid 2FA code.";
        }
    } else {
        $error = "2FA session expired or missing.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>2FA Verification | Chroma Spark</title>
    <link rel="icon" type="image/png" href="Chromaspark.png.jpeg">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(250deg, #fdb43b, #0d0235);
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .verify-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: #0d0235;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #fdb43b;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>

<form action="verify_2fa.php" method="post" class="verify-container">
    <h2>Enter 2FA Code</h2>

    <?php if (isset($error)): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <input type="text" name="code" placeholder="6-digit code" required maxlength="6">
    <button type="submit">Verify</button>
</form>

</body>
</html>
