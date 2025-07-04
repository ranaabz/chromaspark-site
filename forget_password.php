<!-- forgot_password.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | Chroma Spark</title>
    <link rel="icon" type="image/png" href="Chromaspark.png.jpeg">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(250deg, #fdb43b, #0d0235);
        }

        .reset-container {
            background: white;
            padding: 40px 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            width: 360px;
            text-align: center;
        }

        .reset-container h2 {
            margin-bottom: 20px;
            color: #0d0235;
        }

        .reset-container input[type="email"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .reset-container button {
            width: 100%;
            padding: 12px;
            background-color: #0d0235;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .reset-container button:hover {
            background-color: #fdb43b;
            color: #0d0235;
        }

        .back-link {
            margin-top: 15px;
            font-size: 14px;
        }

        .back-link a {
            color: #0d0235;
            text-decoration: none;
        }

        .back-link a:hover {
            text-decoration: underline;
        }

        .message-box {
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <form action="send_reset_link.php" method="POST" class="reset-container">
        <h2>Forgot Password</h2>

        <!-- Display messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="message-box success">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php elseif (isset($_GET['error'])): ?>
            <div class="message-box error">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit">Send Reset Link</button>
        <div class="back-link">
            <a href="login.php">Back to Login</a>
        </div>
    </form>
</body>
</html>
