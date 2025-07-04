<?php
$host = "localhost";
$user = "root"; // Change if needed
$password = "";
$database = "chroma_spark"; 

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    // Connection successful (optional: for debugging)
    // echo("Connection success");
}
?>
