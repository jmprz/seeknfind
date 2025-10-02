<?php
session_start();
include("connection.php");
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['verification_code'])) {
        $verification_code = $_POST['verification_code'];

        $query = "SELECT * FROM user WHERE verification_code = '$verification_code' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);

            // Update user to set verification code to null and mark as verified
            $query = "UPDATE user SET verification_code = NULL, email_verified = 1 WHERE user_id = " . $user_data['user_id'];
            if (mysqli_query($con, $query)) {
                // Log in the user and redirect to index.php
                $_SESSION['user_id'] = $user_data['user_id'];
                header("Location: index.php");
                exit();
            } else {
                $error_message = "Error: " . mysqli_error($con);
            }
        } else {
            $error_message = "Invalid verification code!";
        }
    } else {
        $error_message = "Please enter the verification code!";
    }
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
    <title>Email Verification</title>
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
            <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Check your Spam Messages on your Email</h2>
                        <?php
                        if (!empty($error_message)) {
                            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                        }
                        ?>
                        <form method="post" action="verify_email.php">
                            <div class="mb-3">
                                <label for="verification_code" class="form-label">Enter Verification Code:</label>
                                <div class="input-group mb-3">
                    <span class="input-group-text" id="basic-addon1">
                    <i class='bx bx-envelope bx-sm'></i>
                    </span>
                                <input type="text" class="form-control input-group-form" id="verification_code" name="verification_code" maxlength="6" required>
                            </div>
                            </div>
                            <div class="d-grid my-3">
                                <button class="btn btn_search snf_view btn-lg fw-semibold" type="submit">Submit</button>
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
</body>
</html>
