<?php
// Start the session
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to the login page or homepage
header("Location: http://localhost/ria/auth/login.php"); // Change to your login page URL
exit;
?>
