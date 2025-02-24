<?php
$host = "localhost";
$user = "root"; // Change if needed
$password = ""; // Change if using a password
$dbname = "yena"; 

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
