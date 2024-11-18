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

    // Fetch current item details
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $itemId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $item = $result->fetch_assoc();

        // Fetch the photos for this item
        $stmt = $conn->prepare("SELECT * FROM item_photos WHERE item_id = ?");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $photosResult = $stmt->get_result();
        $photos = [];
        while ($row = $photosResult->fetch_assoc()) {
            $photos[] = $row;
        }

        // Update the item if the form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = htmlspecialchars(trim($_POST['name']));
            $description = htmlspecialchars(trim($_POST['description']));
            $uploadedPhotos = [];
            $deletedPhotos = isset($_POST['delete_photos']) ? $_POST['delete_photos'] : [];

            // Validate input
            if (empty($name) || empty($description)) {
                $error = "Name and description are required.";
            } else {
                // Update item details in the database
                $stmt = $conn->prepare("UPDATE items SET name = ?, description = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $description, $itemId);
                if ($stmt->execute()) {
                    // Handle deleting photos
                    if (!empty($deletedPhotos)) {
                        foreach ($deletedPhotos as $photoId) {
                            // Delete the photo from the database
                            $stmt = $conn->prepare("SELECT photo FROM item_photos WHERE id = ?");
                            $stmt->bind_param("i", $photoId);
                            $stmt->execute();
                            $photoResult = $stmt->get_result();
                            if ($photoResult->num_rows > 0) {
                                $photo = $photoResult->fetch_assoc();
                                $photoPath = '../utilities/uploads/' . $photo['photo'];
                                // Delete the file from the server
                                if (file_exists($photoPath)) {
                                    unlink($photoPath);
                                }
                            }
                            // Delete photo record from the database
                            $stmt = $conn->prepare("DELETE FROM item_photos WHERE id = ?");
                            $stmt->bind_param("i", $photoId);
                            $stmt->execute();
                        }
                    }

                    // Handle uploading new photos
                    if (isset($_FILES['photos'])) {
                        foreach ($_FILES['photos']['name'] as $key => $photoName) {
                            $fileTmp = $_FILES['photos']['tmp_name'][$key];
                            $fileSize = $_FILES['photos']['size'][$key];
                            $fileError = $_FILES['photos']['error'][$key];
                            $fileExt = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));

                            // Check for errors
                            if ($fileError === UPLOAD_ERR_OK) {
                                // Validate file extension
                                if (in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                                    // Validate file size (max 20MB per file)
                                    if ($fileSize <= 20 * 1024 * 1024) {
                                        // Generate unique file name
                                        $fileNewName = uniqid('', true) . '.' . $fileExt;
                                        $fileDestination = '../utilities/uploads/' . $fileNewName;

                                        // Move the uploaded file to the desired folder
                                        if (move_uploaded_file($fileTmp, $fileDestination)) {
                                            // Insert the new photo into the database
                                            $stmt = $conn->prepare("INSERT INTO item_photos (item_id, photo) VALUES (?, ?)");
                                            $stmt->bind_param("is", $itemId, $fileNewName);
                                            $stmt->execute();
                                        } else {
                                            $errors[] = "Failed to upload file: $photoName";
                                        }
                                    } else {
                                        $errors[] = "File is too large: $photoName. Max size is 20MB.";
                                    }
                                } else {
                                    $errors[] = "Invalid file type: $photoName. Only JPG, PNG, and GIF are allowed.";
                                }
                            } else {
                                $errors[] = "Error uploading file: $photoName";
                            }
                        }
                    }

                    // Redirect to success page or the item page
                    $success = "Item updated successfully.";
                } else {
                    $error = "Failed to update item.";
                }
            }
        }
    } else {
        $error = "Item not found or you do not have permission to update it.";
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
    <title>RIA Update Item</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('../header.php');?>
    <div class="container mt-4">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <h3>Update Item</h3>
        <form action="edit.php?id=<?php echo $itemId; ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="itemName" class="form-label">Item Name</label>
                <input type="text" class="form-control" id="itemName" name="name" value="<?php echo htmlspecialchars($item['name']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="itemDescription" class="form-label">Description</label>
                <textarea class="form-control" id="itemDescription" name="description" rows="4" required><?php echo htmlspecialchars($item['description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="photos" class="form-label">Upload New Photos (Optional)</label>
                <input type="file" class="form-control-file" id="photos" name="photos[]" multiple>
            </div>

            <div class="mb-3">
                <label class="form-label">Current Photos</label>
                <div class="row">
                    <?php foreach ($photos as $photo): ?>
                        <div class="col-3">
                            <img src="../utilities/uploads/<?php echo $photo['photo']; ?>" class="img-fluid" alt="photo">
                            <label class="form-check-label">
                                <input type="checkbox" name="delete_photos[]" value="<?php echo $photo['id']; ?>"> Delete
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Update Item</button>
        </form>
        <a href="delete_item.php?id=<?php echo $itemId; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this item?')">Delete</a>

        <a href="../<?php echo ($_SESSION['role'] == 'admin' ? 'admin' : 'user'); ?>/dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</body>
</html>
