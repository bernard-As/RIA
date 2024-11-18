<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "210601312";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
$isLogin = false;
if(isset($_SESSION['user_id'])){
    $isLogin = true;
}
// ini_set('display_errors', 0);
// ini_set('log_errors', 1);
// error_reporting(E_ALL);
?>