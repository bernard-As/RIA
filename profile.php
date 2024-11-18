<?php
// Include the database connection
include 'includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $firstname = htmlspecialchars(trim($_POST['firstname']));
    $lastname = htmlspecialchars(trim($_POST['lastname']));
    $email = htmlspecialchars(trim($_POST['email']));
    $oldPassword = isset($_POST['old_password']) ? htmlspecialchars(trim($_POST['old_password'])) : '';
    $newPassword = isset($_POST['new_password']) ? htmlspecialchars(trim($_POST['new_password'])) : '';
    $confirmPassword = isset($_POST['confirm_password']) ? htmlspecialchars(trim($_POST['confirm_password'])) : '';

    // Validate inputs
    if (empty($firstname) || empty($lastname) || empty($email)) {
        $error = "Firstname, lastname, and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Handle profile picture upload
        $profilePicture = null;
        if (!empty($_FILES['profile_picture']['name'])) {
            $fileTmp = $_FILES['profile_picture']['tmp_name'];
            $fileExt = strtolower(pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION));

            if (!in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                $error = "Invalid file type. Only JPG, PNG, and GIF are allowed.";
            } elseif ($_FILES['profile_picture']['size'] > 5 * 1024 * 1024) {
                $error = "File size exceeds the limit of 5MB.";
            } else {
                $profilePicture = uniqid('profile_', true) . '.' . $fileExt;
                $fileDestination = 'utilities/uploads/' . $profilePicture;

                if (!move_uploaded_file($fileTmp, $fileDestination)) {
                    $error = "Failed to upload profile picture.";
                }
            }
        }

        // If no error, update the profile
        if (!$error) {
            $query = "UPDATE users SET firstname = ?, lastname = ?, email = ?";
            $params = [$firstname, $lastname, $email];
            $types = "sss";

            // Add profile picture to the query if uploaded
            if ($profilePicture) {
                $query .= ", profile_picture = ?";
                $params[] = $profilePicture;
                $types .= "s";
            }
            $query .= " WHERE id = ?";
            $params[] = $userId;
            $types .= "i";

            $stmt = $conn->prepare($query);
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                $success = "Profile updated successfully.";
            } else {
                $error = "Failed to update profile.";
            }

            $stmt->close();

            // Handle password update
            if (!empty($oldPassword) && !empty($newPassword) && !empty($confirmPassword)) {
                if ($newPassword !== $confirmPassword) {
                    $error = "New password and confirm password do not match.";
                } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/', $newPassword)) {
                    $error = "Password must be at least 8 characters long and contain an uppercase letter, a lowercase letter, a number, and a special character.";
                }else {
                    // Fetch the user's current password
                    $query = "SELECT password FROM users WHERE id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $userId);
                    $stmt->execute();
                    $stmt->bind_result($hashedPassword);
                    $stmt->fetch();
                    $stmt->close();
                    // Verify old password
                    if (!password_verify($oldPassword, $hashedPassword)) {
                        $error = "Old password is incorrect.";
                    } else {
                        // Hash the new password and update
                        $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
                        $updateStmt = $conn->prepare($updateQuery);
                        $updateStmt->bind_param("si", $newHashedPassword, $userId);

                        if ($updateStmt->execute()) {
                            $success .= " Password updated successfully.";
                        } else {
                            $error = "Failed to update password.";
                        }

                        $updateStmt->close();
                    }
                }
            }
        }
    }
}

// Fetch user information
$query = "SELECT firstname, lastname, email, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>RIA Profile</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

</head>

<body>
    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">
<?php include('header.php')?>

<div class="container mt-5">
    <h2 class="mb-4">Profile Management</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="profile.php" method="POST" enctype="multipart/form-data">
        <!-- First Name -->
        <div class="mb-3">
            <label for="firstname" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstname" name="firstname" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="lastname" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastname" name="lastname" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <!-- Profile Picture -->
        <div class="mb-3">
            <label for="profile_picture" class="form-label">Profile Picture</label>
            <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
            <?php if (!empty($user['profile_picture'])): ?>
                <img src="http://localhost/ria/utilities/uploads/<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="img-fluid mt-2" style="max-width: 150px;">
            <?php endif; ?>
        </div>

        <hr>

        <h4 class="mb-3">Update Password</h4>

        <!-- Old Password -->
        <div class="mb-3">
            <label for="oldPassword" class="form-label">Old Password</label>
            <input type="password" class="form-control" id="oldPassword" name="old_password">
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="new_password">
        </div>

        <!-- Confirm New Password -->
        <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirm_password">
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
</div>
</div>
</div>


</body>
</html>
