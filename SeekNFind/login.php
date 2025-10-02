<?php
session_start();

include("connection.php");
include("functions.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email_address = $_POST['email_address'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    if (!empty($email_address) && !empty($password)) {
        // Query user by email address
        $query = "SELECT * FROM user WHERE email_address = '$email_address' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);

            // Verify password using password_verify function
            if (password_verify($password, $user_data['password'])) {
                if ($user_data['email_verified'] == 1) {
                    $_SESSION['user_id'] = $user_data['user_id'];
                    $_SESSION['account_type'] = $user_data['account_type'];

                    // Set the "Remember Me" cookie
                    if ($remember_me) {
                        setcookie('user_id', $user_data['user_id'], time() + (86400 * 30), "/"); // 86400 = 1 day, cookie expires in 30 days
                    }

                    if ($user_data['account_type'] === 'Admin') {
                        $_SESSION['is_admin'] = 1;
                        // Redirect to admin dashboard for admin users
                        header("Location: admin_dashboard.php");
                        exit;
                    } else {
                        // Redirect to regular dashboard or homepage for non-admin users
                        header("Location: index.php");
                        exit;
                    }
                } else {
                    // Redirect to verification page if email is not verified
                    header("Location: verify_email.php?email=" . urlencode($email_address));
                    exit();
                }
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
    } else {
        $error_message = "Please enter both email and password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
<link rel="manifest" href="favicon/site.webmanifest">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>SeekNFind | Login</title>
</head>
<style>
@media (max-width: 576px) {
html body {
background-color: white;
}
}
</style>
<body>
<section class="py-3 py-md-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-9 col-lg-6 col-xl-6 col-xxl-6">
                <div class="card card_login border border-light-subtle rounded-4 snf_shadow">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="text-center mb-3">
                            <img src="img/logosnf.svg" alt="SeekNFind Logo" width="70">
                            <h1 class="fw-bold">SeekNFind</h1>
                        </div>
                        <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Log In to your account to continue</h2>
                        <?php
                        if (!empty($error_message)) {
                            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                        }
                        ?>
                        <form method="post" action="">
                            <div class="row gy-2 overflow-hidden">
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class='bx bx-envelope bx-sm'></i>
                                        </span>
                                        <input type="email" class="form-control input-group-form" name="email_address" id="email" placeholder="Email" required>
                                    </div>
                                </div>
                                <div class="col-12 password-toggle">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon2">
                                            <i class='bx bx-lock-alt bx-sm'></i>
                                        </span>
                                        <input type="password" class="form-control input-group-form " name="password" id="password" placeholder="Password" required>
                                        <i class='bx bx-hide bx-sm toggle-icon' onclick="togglePassword('password', this)"></i>
                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-between align-items-center">
                                <div class="form-check">
                                        <input class="form-check-input custom-switch" type="checkbox" name="remember_me" id="remember_me">
                                        <label class="form-check-label" for="remember_me">Remember Me</label>
                                    </div>
                                    <p class="m-0 text-secondary text-center">
                                        <a href="forgot_password.php" class="text-decoration-none" style="color: #526afeff;">Forgot Password?</a>
                                    </p>
                                </div>
                                <div class="col-12">
                                    <div class="d-grid my-3">
                                        <button class="btn btn_search snf_view btn-lg fw-semibold" type="submit">Log in</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="m-0 text-secondary text-center">Don't have an account? <a href="signup.php" class="text-decoration-none" style="color: #526afeff;">Sign Up Here</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
function togglePassword(fieldId, icon) {
    var field = document.getElementById(fieldId);
    if (field.type === "password") {
        field.type = "text";
        icon.classList.remove('bx-hide');
        icon.classList.add('bx-show');
    } else {
        field.type = "password";
        icon.classList.remove('bx-show');
        icon.classList.add('bx-hide');
    }
}
</script>
</body>
</html>
