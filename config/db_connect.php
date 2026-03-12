<?php
$email= "localhost";
$fullname = "root";
$password = "";
$database = "database.sql";

$conn = new mysqli($email, $fullname, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>