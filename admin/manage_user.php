<?php
include '../includes/db.php'; // Include your DB connection
// Check if the user is an admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Handle user deletion
if (isset($_GET['delete'])) {
    $userId = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
    $deleteMsg = "User deleted successfully.";
}

// Handle search query
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';

// Fetch users based on search or all users
$query = $search 
    ? "SELECT * FROM users WHERE firstname LIKE ? OR lastname LIKE ? OR email LIKE ?"
    : "SELECT * FROM users";
$stmt = $conn->prepare($query);
if ($search) {
    $searchParam = "%$search%";
    $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);
}
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Admin Dashboard</title>
</head>
<body>
    <?php include '../header.php'?>
<div class="container mt-4">
    <h1 class="mb-4">Admin Dashboard</h1>
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">User updated successfully.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">An error occurred: <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['created'])&&$_GET['created']=='success'): ?>
    <div class="alert alert-success">User created successfully.</div>
    <?php elseif (isset($_GET['error'])): ?>
        <div class="alert alert-danger">An error occurred: <?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>
    <!-- Search Form -->
    <form method="GET" action="" class="d-flex mb-4">
        <input type="text" class="form-control me-2" name="search" value="<?php echo $search; ?>" placeholder="Search by name or email...">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if (isset($deleteMsg)): ?>
        <div class="alert alert-success"><?php echo $deleteMsg; ?></div>
    <?php endif; ?>

    <!-- Users Table -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Username</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo $user['firstname']; ?></td>
                <td><?php echo $user['lastname']; ?></td>
                <td><?php echo $user['email']; ?></td>
                <td><?php echo $user['username']; ?></td>
                <td>
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="?delete=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="btn btn-danger btn-sm">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Create New User -->
    <h2>Create New User</h2>
    <form method="POST" action="create_user.php">
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" required>
        </div>
        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
            <label for="role" class="form-label">User Role</label>
            <select class="form-control" id="role" name="role" required>
                <option value="user">User</option>
                <option value="admin">Admin</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>

</body>
</html>
