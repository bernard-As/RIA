<?php

// Include the database connection
include '../includes/db.php';

// Initialize response messages
$error = '';
$success = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Creating An Item
            $name = htmlspecialchars(trim($_POST['name']));
            $description = htmlspecialchars(trim($_POST['description']));
    
            // Validate the input data
            if (empty($name) || empty($description)) {
                $error = "Name and description are required.";
            } elseif (strlen($name) > 255) {
                $error = "Item name should not exceed 255 characters.";
            } elseif (!isset($_FILES['photos'])) {
                $error = "A valid picture file is required.";
            } else {
                foreach ($_FILES['photos']['name'] as $key => $photoName) {
                    $fileTmp = $_FILES['photos']['tmp_name'][$key];
                    $fileSize = $_FILES['photos']['size'][$key];
                    $fileError = $_FILES['photos']['error'][$key];
                    $fileExt = strtolower(pathinfo($photoName, PATHINFO_EXTENSION));
        
                    // Check for errors
                    if ($fileError !== UPLOAD_ERR_OK) {
                        $errors[] = "Error uploading file: $photoName";
                        continue;
                    }
        
                    // Validate file extension
                    if (!in_array($fileExt, ['jpg', 'jpeg', 'png', 'gif'])) {
                        $errors[] = "Invalid file type: $photoName. Only JPG, PNG, and GIF are allowed.";
                        continue;
                    }
        
                    // Validate file size (max 5MB per file)
                    if ($fileSize > 20 * 1024 * 1024) {
                        $errors[] = "File is too large: $photoName. Max size is 20MB.";
                        continue;
                    }
        
                    // Generate unique file name
                    $fileNewName = uniqid('', true) . '.' . $fileExt;
                    $fileDestination = '../utilities/uploads/' . $fileNewName;
        
                    // Move the uploaded file to the desired folder
                    if (move_uploaded_file($fileTmp, $fileDestination)) {
                        $uploadedPhotos[] = $fileNewName;  // Save the file name for later insertion into DB
                    } else {
                        $errors[] = "Failed to upload file: $photoName";
                    }
                }
                if(empty($errors)){
                    $userId = $_SESSION['user_id'];
                    $stmt = $conn->prepare("INSERT INTO items (name, description, user_id) VALUES (?, ?, ?)");
                    $stmt->bind_param("ssi", $name, $description, $userId);
                    $stmt->execute();
                    $itemId = $stmt->insert_id;  // Get the inserted item ID
        
                    // Insert each uploaded photo into the database
                    foreach ($uploadedPhotos as $photo) {
                        $stmt = $conn->prepare("INSERT INTO item_photos (item_id, photo) VALUES (?, ?)");
                        $stmt->bind_param("is", $itemId, $photo);
                        $stmt->execute();
                    }
        
                    $success =  "Item created successfully with " . count($uploadedPhotos) . " photo(s).";
               
                        $stmt->close();
                    }else{
                        $error = "Error creating item: " . implode("<br>", $errors);
                    }
                }
            }
    
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">

    <title>RIA Create Item</title>

    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">
            <?php include('../header.php');?>
            <!-- Main Content -->
<center>
            <div id="content">

   <!-- Begin Page Content -->
   <div class="container-fluid">
   <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
<div class="col-xl-8 col-lg-7">
  <div class="card shadow mb-4">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
      <h6 class="m-0 font-weight-bold text-primary">Create an Item</h6>
    </div>
    <!-- Card Body -->
    <div class="card-body">
        
      <form action="create_item.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="item" value="1"/>
        <!-- Item Name -->
        <div class="mb-3">
          <label for="itemName" class="form-label">Item Name</label>
          <input type="text" class="form-control" id="itemName" name="name" placeholder="Enter item name" required>
        </div>

        <!-- Item Description -->
        <div class="mb-3">
          <label for="itemDescription" class="form-label">Description</label>
          <textarea class="form-control" id="itemDescription" name="description" rows="4" placeholder="Enter item description" required></textarea>
        </div>

        <!-- Upload Picture -->
        <div class="form-group">
            <label for="photos">Upload Photos</label>
            <input type="file" class="form-control-file" id="photos" name="photos[]" multiple required>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Item</button>
      </form>
    </div>
  </div>
</div>
<div>
<div class=" justify-content-between mb-4">
                        <a href="../<?php echo ($_SESSION['role'] == 'admin' ? 'admin' : 'user'); ?>/dashboard.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-download fa-sm text-white-50"></i>Go to dashboard</a>
                    </div>

</div>
    </center>
<footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; RIA 2024</span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="login.html">Logout</a>
                </div>
            </div>
        </div>
    </div>



</body>

</html>