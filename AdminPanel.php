<?php
$host = getenv("host") ?: "localhost";
$port = getenv("port") ?: 3306;
$user = getenv("user") ?: "root";
$password = getenv("pass") ?: "";
$database = getenv("name") ?: "chroma_spark";

// Create connection
$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
