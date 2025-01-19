<?php 

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Call this function to get or create a CSRF token
$csrfToken = generateCsrfToken();
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);


// Set the Access-Control-Allow-Origin header
$allowedOrigins = [
    'http://localhost',
    'https://api.hcaptcha.com/',
    'https://newassets.hcaptcha.com ',
    'https://hcaptcha.com/ '
];

// Get the origin of the request
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

// Check if the origin is in the list of allowed origins
if (in_array($origin, $allowedOrigins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Credentials: true"); // If you need to include credentials
} else {
    // Optionally, you can set a default origin or deny the request
    // header("Access-Control-Allow-Origin: *"); // Allow all (not recommended)
    // Or simply do nothing to deny the request
}

// Additional CORS headers (optional but often required)
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Max-Age: 86400"); // Cache for 24 hours

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(); // End the script as no further processing is needed for OPTIONS requests
}


// Define the CSP policy
$nonce = base64_encode(random_bytes(16));
$cspPolicy = "default-src 'self' https://newassets.hcaptcha.com https://hcaptcha.com/ https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://api.hcaptcha.com/ https://api.hcaptcha.com/;  " .
             "script-src 'self' https://cdn.jsdelivr.net https://hcaptcha.com https://newassets.hcaptcha.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://api.hcaptcha.com/; " .
             "style-src 'self'  https://newassets.hcaptcha.com https://cdn.jsdelivr.net https://hcaptcha.com/ https://fonts.googleapis.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
             "img-src 'self' data: https://cdn.jsdelivr.net https://hcaptcha.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
             "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com/ https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
             "connect-src 'self' https://apis.example.com https://hcaptcha.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " .
             "frame-src 'self' https://hcaptcha.com https://newassets.hcaptcha.com https://cdnjs.cloudflare.com https://cdn.jsdelivr.net; " . // Added frame-src directive
             "object-src 'none' https://api.hcaptcha.com/;".
             "frame-ancestors 'self'; " . // Explicitly define frame-ancestors
             "form-action 'self' https://api.hcaptcha.com/; " 
            //  "nonce-$nonce"
             ;

// Set the CSP header
header("Content-Security-Policy: $cspPolicy");

// Optionally, you can also set the report-only header for testing purposes
$cspReportOnlyPolicy = "default-src 'self' https://newassets.hcaptcha.com https://hcaptcha.com https://cdn.jsdelivr.net https://api.hcaptcha.com/; 
                        report-uri /csp-violation-report-endpoint/";
header("Content-Security-Policy-Report-Only: $cspReportOnlyPolicy");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Create Items - RIA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="includes/style.css" rel="stylesheet">
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
