<?php

// Include database connection
include '../includes/db.php';

// Initialize error message
$error = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $captcha = $_POST['h-captcha-response'];

    if (!$captcha) {
        $error =  $error .'Please complete the captcha.';
    }else{
    // Send the hCaptcha response to the hCaptcha API for validation
        $secretKey = "ES_6186f67705394ad1a39f38d7dec0ef2e"; // Replace with your actual secret key
        $response = file_get_contents("https://hcaptcha.com/siteverify?secret=$secretKey&response=$captcha");
        $responseKeys = json_decode($response, true);
        $captcha_ok = false;
        if (intval($responseKeys["success"]) !== 1) {
            $error .=  'Failed captcha verification. Please try again.';
        } else {
            $captcha_ok = true;
        }

        if ($captcha_ok){
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            // Validate input
            if (empty($email) || empty($password)) {
                $error = "Email and password are required.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Invalid email format.";
            } else {
                // Check the database for email and password match
                $stmt = $conn->prepare("SELECT id, password,firstname,lastname, role FROM users WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows === 1) {
                    $user = $result->fetch_assoc();

                    // Verify the password
                    if (password_verify($password, $user['password'])) {
                        // Store user ID in session and redirect to a dashboard
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['firstname'] = $user['firstname'];
                        $_SESSION['lastname'] = $user['lastname'];
                        if($user['role']=='user')
                            header("Location: ../user/dashboard.php");
                        elseif($user['role']=='admin')
                            header("Location: ../admin/dashboard.php");
                            exit;
                    } else {
                        $error = "Incorrect password.";
                    }
                } else {
                    $error = "No account found with that email.";
                }

                $stmt->close();
            }
        }
    }
}
$HCAPTCHA_KEY = "9d389074-6564-481c-8198-2ade27a8211c";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>RIA Login</title>

    <!-- Custom fonts for this template-->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-primary">

    <div class="container">

        <div class="card o-hidden border-0 shadow-lg my-5">
            <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                    <div class="col-lg-5 d-none d-lg-block bg-login-image"></div>
                    <div class="col-lg-7">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                            </div>
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>

                            <form class="user" method="POST" action="login.php" csrf_token=<?php echo htmlspecialchars($csrfToken); ?>>
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken); ?>">
                                <div class="form-group mb-3">
                                    <input type="email" class="form-control form-control-user" id="nputEmail"
                                           placeholder="Email Address" name="email" required>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="password" class="form-control form-control-user" id="nputPassword"
                                           placeholder="Password" name="password" required>
                                </div>
                                <div class="h-captcha" data-sitekey=<?php echo $HCAPTCHA_KEY  ?>></div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Login
                                </button>
                            </form>
                            <hr>
                            <div class="text-center">
                                <a class="small" href="register.php">Create an Account!</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://hcaptcha.com/1/api.js" async defer></script>

</body>

</html>
