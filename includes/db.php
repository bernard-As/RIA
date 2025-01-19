<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "210601312";
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
session_start();
$isLogin = false;
if(isset($_SESSION['user_id'])){
    $isLogin = true;
}
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
    'https://api.hcaptcha.com/'
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
$cspPolicy = "default-src 'self' https://newassets.hcaptcha.com https://hcaptcha.com/ https://api.hcaptcha.com/;  " .
             "script-src 'self' https://cdn.jsdelivr.net https://hcaptcha.com https://newassets.hcaptcha.com https://hcaptcha.com/1/api.js https://cdn.jsdelivr.net/ https://api.hcaptcha.com/; " .
             "style-src 'self' 'unsafe-inline'  https://newassets.hcaptcha.com https://cdn.jsdelivr.net https://hcaptcha.com/ https://fonts.googleapis.com; " .
             "img-src 'self' data: https://cdn.jsdelivr.net https://hcaptcha.com; " .
             "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com/ https://fonts.googleapis.com; " .
             "connect-src 'self' https://apis.example.com https://hcaptcha.com; " .
             "frame-src 'self' https://hcaptcha.com https://newassets.hcaptcha.com; " . // Added frame-src directive
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