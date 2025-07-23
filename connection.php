<?php
// PostgreSQL connection using pg_connect
$host = "dpg-d209sfre5dus73d5rfsg-a";    // e.g., "localhost" or your Render host
$port = "5432";            // default PostgreSQL port
$dbname = "chroma_spark_nfki";
$user = "chroma_spark_nfki_user";
$password = "MJfUXNzs4727vpq98rdbLtiPRDVTrILE";

$conn_string = "host=$host port=$port dbname=$dbname user=$user password=$password";
$dbconn = pg_connect($conn_string);

if (!$dbconn) {
    die("Error in connection: " . pg_last_error());
}
?>
