<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Ensure this path is correct

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data safely
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $country = trim($_POST['country']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);

    // Connect to PostgreSQL - update credentials as needed or use environment variables
    $dbconn = pg_connect("host=dpg-d209sfre5dus73d5rfsg-a dbname=chroma_spark_nfki user=chroma_spark_nfki_user password=MJfUXNzs4727vpq98rdbLtiPRDVTrILE port=5432");
    if (!$dbconn) {
        die("Connection failed: " . pg_last_error());
    }

    // Insert using prepared statement to avoid SQL injection
    $sql = "INSERT INTO contact_messages (first_name, last_name, country, email, subject) VALUES ($1, $2, $3, $4, $5)";
    $params = [$first_name, $last_name, $country, $email, $subject];

    $result = pg_query_params($dbconn, $sql, $params);

    if ($result) {
        // Send email notification to admin
        $mail = new PHPMailer(true);
        try {
            // SMTP setup
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'chromaspark0@gmail.com'; // Your Gmail
            $mail->Password = 'YOUR_APP_PASSWORD';      // Your Gmail App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Email info
            $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark Website');
            $mail->addAddress('chromaspark0@gmail.com', 'Admin');

            $mail->isHTML(true);
            $mail->Subject = "New Contact Form Submission from $first_name $last_name";
            $mail->Body = "
                <h2>New Contact Message</h2>
                <p><strong>Name:</strong> {$first_name} {$last_name}</p>
                <p><strong>Country:</strong> {$country}</p>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($subject)) . "</p>
            ";
            $mail->AltBody = "New contact from {$first_name} {$last_name}\nCountry: {$country}\nEmail: {$email}\nMessage:\n{$subject}";

            $mail->send();

            // Redirect on success
            header("Location: contact_success.html");
            exit();
        } catch (Exception $e) {
            echo "❌ Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "❌ Database Error: " . pg_last_error($dbconn);
    }

    pg_close($dbconn);
} else {
    echo "❌ Invalid Request!";
}
