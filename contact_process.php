<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure this is the correct path

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $country = $_POST['country'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];

    // Connect to PostgreSQL database
    $dbconn = pg_connect("host=your_host dbname=your_db user=your_user password=your_password port=your_port");
    if (!$dbconn) {
        die("Connection failed: " . pg_last_error());
    }

    // Insert into database using pg_query_params to avoid SQL injection
    $sql = "INSERT INTO contact_messages (first_name, last_name, country, email, subject) VALUES ($1, $2, $3, $4, $5)";
    $params = array($first_name, $last_name, $country, $email, $subject);

    $result = pg_query_params($dbconn, $sql, $params);

    if ($result) {
        // Send email to Chroma Spark admin using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'chromaspark0@gmail.com';
            $mail->Password = 'ssiz jqwh ugze puqy'; // App password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Recipients
            $mail->setFrom('chromaspark0@gmail.com', 'Chroma Spark Website');
            $mail->addAddress('chromaspark0@gmail.com', 'Admin');

            // Content
            $mail->isHTML(true);
            $mail->Subject = "New Contact Form Submission from $first_name $last_name";
            $mail->Body = "
                <h2>New Contact Message</h2>
                <p><strong>Name:</strong> $first_name $last_name</p>
                <p><strong>Country:</strong> $country</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Message:</strong><br>$subject</p>
            ";
            $mail->AltBody = "New contact from $first_name $last_name\nCountry: $country\nEmail: $email\nMessage:\n$subject";

            $mail->send();
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
?>
