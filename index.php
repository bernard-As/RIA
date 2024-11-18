<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Items - RIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }
        .hero-section {
            background: url('path/to/your/image.jpg') no-repeat center center;
            background-size: cover;
            color: white;
            padding: 100px 0;
            text-align: center;
        }
        .feature-icon {
            font-size: 50px;
            color: #007bff;
        }
        .cta-button {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
        }
        .cta-button:hover {
            background-color: #0056b3;
        }
        .feature-section {
            padding: 60px 0;
        }
        .item-form-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }
        .item-form-container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>

    <!-- Header Section -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top shadow">
        <div class="container">
            <a class="navbar-brand" href="#">RIA</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="auth/login.php">Create Item</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <h1 class="display-4">Create and Manage Your Items Easily</h1>
            <p class="lead">Add items with a name, description, and multiple photos. Streamline your inventory management and showcase your creations.</p>
            <a href="auth/register.php" class="cta-button">Register</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="feature-section">
        <div class="container text-center">
            <h2 class="mb-5">Why Create Items with RIA?</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <i class="feature-icon fas fa-box"></i>
                    <h4>Organize Your Items</h4>
                    <p>Easily manage all your items by providing key details like name, description, and images.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="feature-icon fas fa-image"></i>
                    <h4>Multiple Photos</h4>
                    <p>Upload multiple photos for each item to showcase its features and details.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <i class="feature-icon fas fa-edit"></i>
                    <h4>Simple Editing</h4>
                    <p>Edit item details and photos effortlessly whenever you need to update your catalog.</p>
                </div>
            </div>
        </div>
    </section>

    <

    <!-- Call to Action Section -->
    <section class="bg-primary text-white text-center py-5">
        <div class="container">
            <h2 class="mb-4">Start Creating Your Items Now!</h2>
            <p class="mb-4">Build your inventory and showcase your items easily. Get started by creating a new item now.</p>
            <a href="auth/login.php" class="cta-button">Login</a>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-dark text-white text-center py-4">
        <p>&copy; 2024 RIA. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
