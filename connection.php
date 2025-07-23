<?php
$host = getenv("DB_HOST");
$db = getenv("DB_NAME");
$user = getenv("DB_USER");
$pass = getenv("DB_PASS");
$port = getenv("DB_PORT");

$conn_string = "host=$host dbname=$db user=$user password=$pass port=$port";

$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    die("Connection failed: " . pg_last_error());
}

echo "Connected to PostgreSQL successfully!";
?>
