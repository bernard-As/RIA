<?php
// Include database connection
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header('Location: login.php');
    exit;
}

// Check if item ID is provided in the URL
if (isset($_GET['id'])) {
    $itemId = intval($_GET['id']);
    
    // Check if the item exists and belongs to the current user
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Delete the associated photos first
        $stmt = $conn->prepare("DELETE FROM item_photos WHERE item_id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        
        // Delete the item from the items table
        $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
        $stmt->bind_param("i", $itemId);
        if ($stmt->execute()) {
            $success = "Item deleted successfully.";
        } else {
            $error = "Failed to delete item.";
        }
    } else {
        $error = "Item not found or you do not have permission to delete it.";
    }
} else {
    $error = "Item ID is missing.";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>RIA Delete Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../header.php')?>
    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        <a href="../<?php echo ($_SESSION['role'] == 'admin' ? 'admin' : 'user'); ?>/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
    </div>
</body>
</html>
