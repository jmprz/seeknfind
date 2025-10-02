<?php
session_start();
include("connection.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reset_token = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (!empty($reset_token) && !empty($new_password) && $new_password === $confirm_password) {
        // Validate reset token and check if it's within the expiry time
        $query = "SELECT * FROM user WHERE reset_token = '$reset_token' AND reset_expiry > NOW() LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $user_id = $user_data['user_id'];

            // Hash the new password before updating
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the user's password and clear the reset token
            $update_query = "UPDATE user SET password = '$hashed_password', reset_token = NULL, reset_expiry = NULL WHERE user_id = '$user_id'";
            if (mysqli_query($con, $update_query)) {
                header("Location: login.php?reset=success");
                exit;
            } else {
                $error_message = "Error updating password: " . mysqli_error($con);
            }
        } else {
            $error_message = "Invalid or expired reset token.";
        }
    } else {
        $error_message = "Passwords do not match or fields are empty.";
    }
} else {
    // Fetch the reset token from query parameter if available
    $reset_token = $_GET['token'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <title>Reset Password</title>
</head>
<body>
<section class="py-3 py-md-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 col-xxl-5">
                <div class="card border border-light-subtle rounded-4 snf_shadow">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="text-center mb-3">
                            <img src="img/logosnf.svg" alt="SeekNFind Logo" width="70">
                            <h1 class="fw-bold">SeekNFind</h1>
                        </div>
                        <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Reset Your Password</h2>
                        <?php
                        if (!empty($error_message)) {
                            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                        }
                        ?>
                        <form method="post" action="reset_password.php">
                            <input type="hidden" name="token" value="<?php echo htmlspecialchars($reset_token); ?>">
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <div class="input-group mb-3 password-toggle">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bx-lock-alt bx-sm'></i>
                                    </span>
                                    <input type="password" class="form-control input-group-form" id="new_password" name="new_password" required>
                                    <i class='bx bx-hide bx-sm toggle-icon' onclick="togglePassword('new_password', this)"></i>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group mb-3 password-toggle">
                                    <span class="input-group-text" id="basic-addon2">
                                        <i class='bx bx-lock-alt bx-sm'></i>
                                    </span>
                                    <input type="password" class="form-control input-group-form" id="confirm_password" name="confirm_password" required>
                                    <i class='bx bx-hide bx-sm toggle-icon' onclick="togglePassword('confirm_password', this)"></i>
                                </div>
                            </div>
                            <div class="d-grid my-3">
                                <button class="btn btn_search snf_view btn-lg fw-semibold" type="submit">Reset Password</button>
                            </div>
                        </form>
                        <div class="text-center">
                            <p class="m-0 text-secondary text-center">Back to <a href="login.php" class="text-decoration-none" style="color: #526afeff;">Log in Page</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
