<?php
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered_code = trim($_POST['code']);
    if ($entered_code == $_SESSION['verification_code']) {
        unset($_SESSION['verification_code']);
        header('Location: Admin2.html');
        exit();
    } else {
        $error = "Incorrect verification code!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Verification | Chroma Spark</title>
    <link rel="icon" type="image/png" href="Chromaspark.png.jpeg">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: url('Chromaspark.png.jpeg') no-repeat center center/cover;
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .verify-container {
            background: rgba(0, 0, 0, 0.7);
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            color: white;
            width: 300px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin: 15px 0;
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

        .error {
            color: red;
            margin-top: 10px;
        }

        h2 {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="verify-container">
        <h2>Enter Verification Code</h2>
        <form method="POST">
            <input type="text" name="code" placeholder="6-digit code" required>
            <button type="submit">Verify</button>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
        </form>
    </div>
</body>
</html>
