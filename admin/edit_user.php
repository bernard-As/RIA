<?php
include '../includes/db.php'; // Include your DB connection

// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Get user ID from query string
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage_user.php?error=invalid_user");
    exit;
}

$userId = intval($_GET['id']);

// Fetch user details
$query = "SELECT firstname, lastname, email, username FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: manage_user.php?error=user_not_found");
    exit;
}

// Update user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $username = htmlspecialchars(trim($_POST['username']));

    // Optional: Reset password if provided
    $password = isset($_POST['password']) && !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;

    // Check if the username is unique
    $query = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $username, $userId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "The username is already taken. Please choose another one.";
        $stmt->close();
    } else {
        $stmt->close();

        // Proceed with updating the user details
        $updateQuery = $password
            ? "UPDATE users SET firstname = ?, lastname = ?, email = ?, username = ?, password = ? WHERE id = ?"
            : "UPDATE users SET firstname = ?, lastname = ?, email = ?, username = ? WHERE id = ?";

        $stmt = $conn->prepare($updateQuery);

        if ($password) {
            $stmt->bind_param("sssssi", $firstname, $lastname, $email, $username, $password, $userId);
        } else {
            $stmt->bind_param("ssssi", $firstname, $lastname, $email, $username, $userId);
        }

        if ($stmt->execute()) {
            header("Location: manage_user.php?success=updated_user");
            exit;
        } else {
            $error = "Failed to update user: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Edit User</title>
</head>
<body>
<div class="container mt-4">
    <h1 class="mb-4">Edit User</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo $user['firstname']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo $user['lastname']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">User Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo $user['username']; ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep unchanged)</label>
            <input type="password" class="form-control" id="password" name="password">
        </div>
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="manage_user.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

</body>
</html>
