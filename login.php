<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: client-dashboard.php');
    exit();
}
/*if (isset($_GET['error'])): ?>
    <p style="color: red;">
        <?php
            if ($_GET['error'] == 'user_not_found') {
                echo "User not found. Please sign up.";
            } elseif ($_GET['error'] == 'wrong_password') {
                echo "Incorrect password. Try again.";
            } elseif ($_GET['error'] == 'account_deleted') {
                echo "Your account has been deleted.";
            }
        ?>
    </p>*/
 ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Login | Chroma Spark</title>
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
        .login-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }
        .login-container h2 {
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
        .links {
            margin-top: 10px;
            font-size: 14px;
        }
        .links a {
            color: #0d0235;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<form action="connect2.php" method="post" class="login-container">
    <h2>Client Login</h2>

    <?php if (isset($_GET['message']) && $_GET['message'] == 'deleted'): ?>
            <p class="message">Your account has been deleted.</p>
        <?php endif; ?>
    
    <!-- Show error messages -->
    <?php if (isset($_GET['error'])): ?>
        <p style="color: red;">
            <?php
                if ($_GET['error'] == 'user_not_found') {
                    echo "User not found. Please sign up.";
                } elseif ($_GET['error'] == 'wrong_password') {
                    echo "Incorrect password. Try again.";
                }
            ?>
        </p>
    <?php endif; ?>
    
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    
    <div class="links">
        <a href="Signup.php">Don't have an account? Sign up|</a>
        <a href="forget_password.php">Forgot Password?</a>
    </div>
</form>
</body>
</html>

