<?php
session_start();
include '../includes/db.php'; // Include your DB connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $username = htmlspecialchars(trim($_POST['username']));
    $role = htmlspecialchars(trim($_POST['role']));
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, username, password,role) VALUES (?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssssss", $firstname, $lastname, $email, $username, $password,$role);

    if ($stmt->execute()) {
        header("Location: manage_user.php?created=success");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
