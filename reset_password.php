<?php
include 'connection.php'; // Your PostgreSQL connection ($conn)

// Check if there's a token in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Use pg_query_params for parameterized query to avoid SQL injection
    $sql = "SELECT * FROM password_resets WHERE token = $1";
    $result = pg_query_params($conn, $sql, array($token));

    if ($result && pg_num_rows($result) > 0) {
        // Token is valid - show reset form
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1" />
            <title>Reset Password | Chroma Spark</title>
            <link rel="icon" type="image/png" href="Chromaspark.png.jpeg" />
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
                .reset-container {
                    background: white;
                    padding: 40px;
                    border-radius: 10px;
                    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                    width: 350px;
                    text-align: center;
                }
                .reset-container h2 {
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
                .message-box {
                    padding: 15px;
                    margin: 15px auto;
                    width: 80%;
                    border-radius: 8px;
                    font-size: 16px;
                    text-align: center;
                }
                .message-box.success {
                    background-color: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }
                .message-box.error {
                    background-color: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }
            </style>
        </head>
        <body>
            <form action="update_password.php" method="POST" class="reset-container">
                <h2>Reset Your Password</h2>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>" />
                <input type="password" name="new_password" placeholder="Enter new password" required />
                <button type="submit">Update Password</button>
            </form>
        </body>
        </html>
        <?php
    } else {
        echo "Invalid or expired token.";
    }
} else {
    echo "No token provided.";
}
?>
