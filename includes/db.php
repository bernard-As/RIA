<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "210601312";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>