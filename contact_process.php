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

    // Connect to the database
    $con = new mysqli('localhost', 'root', '', 'chroma_spark');

    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    // Insert into database
    $sql = "INSERT INTO contact_messages (first_name, last_name, country, email, subject) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("sssss", $first_name, $last_name, $country, $email, $subject);

    if ($stmt->execute()) {
        // ✅ Send email to Chroma Spark admin using PHPMailer
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
        echo "❌ Database Error: " . $con->error;
    }

    $stmt->close();
    $con->close();
} else {
    echo "❌ Invalid Request!";
}
?>
