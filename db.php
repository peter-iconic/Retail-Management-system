<?php
$host = "localhost";
$user = "root"; // XAMPP default user
$pass = "";     // XAMPP default password (empty)
$db = "ims";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>