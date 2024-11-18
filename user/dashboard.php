<?php

// Include the database connection
include '../includes/db.php';

// Initialize response messages
$error = '';
$success = '';

$queryT = "
    SELECT
        COUNT(*) AS total_items,
        SUM(CASE WHEN items.user_id = {$_SESSION['user_id']} THEN 1 ELSE 0 END) AS owned_items,
        SUM(CASE WHEN items.user_id != {$_SESSION['user_id']} THEN 1 ELSE 0 END) AS non_owned_items
    FROM items
";
$resultT = $conn->query($queryT);
$CO = $resultT->fetch_assoc();
$query = "
    SELECT 
        items.id AS item_id,
        items.name AS item_name, 
        items.description, 
        CONCAT(COALESCE(users.firstname, ''), ' ', COALESCE(users.lastname, '')) AS owner_name,
        GROUP_CONCAT(item_photos.photo ORDER BY item_photos.id ASC) AS photos,
        -- Add a flag to check if it's owned by the current user
        IF(items.user_id = {$_SESSION['user_id']}, 1, 0) AS is_owned_by_user
    FROM items
    JOIN users ON items.user_id = users.id
    LEFT JOIN item_photos ON items.id = item_photos.item_id
    
";
if(isset($_GET['item_id']))
$query .= "WHERE items.id = ".$_GET['item_id'];
else {
if(isset($_GET['myItems'])){
    $query .= " WHERE items.user_id = {$_SESSION['user_id']} ";
}elseif(isset($_GET['otherItems'])){
    $query .= " WHERE items.user_id != {$_SESSION['user_id']} ";
}}
$query .= " GROUP BY items.id ORDER BY items.id DESC";
$result = $conn->query($query);
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

    <title>RIA Dashboard</title>

    <!-- Custom fonts for this template-->
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->

</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <?php include('../header.php')?>

                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <a href="../items/create_item.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                            class="fas fa-download fa-sm text-white-50"></i>Create an item</a>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-primary shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                                Items on platform</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php 
                                                echo $CO['total_items']
                                            ?><br/>
                                            <a href="?" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                            class="fas fa-download fa-sm text-white-50"></i>Show</a>

                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                My Items</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php 
                                                echo $CO['owned_items']
                                            ?><br/>
                                            <a href="?myItems" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                            class="fas fa-download fa-sm text-white-50"></i>Show</a>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Earnings (Monthly) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-info shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Other items
                                            </div>
                                            <div class="row no-gutters align-items-center">
                                                <div class="col-auto">
                                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                    <?php
                                                        echo $CO['non_owned_items']
                                                    ?>
                                                                <br/>
                                                    <a href="?otherItems" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                                        class="fas fa-download fa-sm text-white-50"></i>Show</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php elseif ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>

                    <!-- Content Row -->
                    <div class="row">

                        <!-- Content Column -->

                        <?php while ($item = $result->fetch_assoc()) { ?>
                            <div class="col-md-6 mb-4" >
                                <div class="card shadow mb-4">
                                    <!-- Card Header with Item Name and Owner Name -->
                                    <div class="card-header py-3 d-flex align-items-center justify-content-between">
                                        <h6 class="m-0 font-weight-bold text-primary">
                                            <?php echo htmlspecialchars($item['item_name']); ?>
                                        </h6>
                                        <span class="text-muted">by <?php echo htmlspecialchars($item['owner_name']); ?></span>
                                    </div>

                                    <!-- Card Body with Images and Description -->
                                    <div class="card-body d-flex">
                                        <!-- Item Images -->
                                        <div class="me-3">
                                            <?php
                                            // Split the photos string into an array
                                            $photos = explode(',', $item['photos']);
                                            foreach ($photos as $photo) {
                                                $photo = trim($photo); // Remove any extra spaces
                                                echo "<img src='http://localhost/ria/utilities/uploads/$photo' alt='Item Image' class='img-fluid rounded' style='max-width: 150px; max-height: 150px; margin-bottom: 10px;'>";
                                            }
                                            ?>
                                        </div>
                                        
                                        <!-- Item Description -->
                                        <div>
                                            <p><?php echo nl2br(htmlspecialchars($item['description'])); ?></p>
                                        </div>
                                    </div>
                                    <div class=" align-items-center justify-content-between mb-4">
                                        <a href="../items/edit.php?id=<?php echo $item['item_id']?>" class="d-none d-sm-inline-block btn btn-sm btn-warning shadow-sm"><i
                                            class="fas fa-download fa-sm text-white-50"></i>Update/Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>


                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
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