<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "store_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
