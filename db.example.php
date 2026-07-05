<?php
// Rename this file to db.php and fill in your local database credentials.
// db.php is gitignored and will not be committed.

$host = "localhost";
$username = "root";
$password = "";
$database = "your_database_name_here";

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
