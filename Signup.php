<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Signup | Chroma Spark</title>
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
        .signup-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }
        .signup-container h2 {
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
            background:#fdb43b;
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
        .error-message {
            color: red;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }
    </style>
</head>
<body>

    <form action="send_verification.php" method="post" class="signup-container" onsubmit="return validateForm()">
        <h2>Client Signup</h2>
        <!-- Added id attributes for validation -->
        <input type="text" name="username" id="username" placeholder="Username" required>
        <input type="email" name="email" id="email" placeholder="Email" required>
        <input type="password" name="password" id="password" placeholder="Password" required>
        <div id="password-error" class="error-message">Password must be at least 6 characters long.</div>
        <button type="submit">Sign Up</button>
        <div class="links">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </form>

    <script>
        function validateForm() {
            // Get form fields
            var password = document.getElementById("password").value;
            var passwordError = document.getElementById("password-error");
            
            // Check if password is too short
            if (password.length < 6) {
                passwordError.style.display = "block"; // Show the error message
                return false; // Prevent form submission
            } else {
                passwordError.style.display = "none"; // Hide the error message
            }

            return true; // Allow form submission
        }
    </script>

</body>
</html>
