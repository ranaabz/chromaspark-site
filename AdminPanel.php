<?php
$host = getenv("DB_HOST") ?: "localhost";
$port = getenv("DB_PORT") ?: 3306;
$user = getenv("DB_USER") ?: "root";
$password = getenv("DB_PASS") ?: "";
$database = getenv("DB_NAME") ?: "chroma_spark";

// Create connection
$conn = new mysqli($host, $user, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
