<?php
session_start();
include("connection.php");
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_address = $_POST['email_address'];

    if (!empty($email_address)) {
        $query = "SELECT * FROM user WHERE email_address = '$email_address' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            $reset_token = bin2hex(random_bytes(16));
            $reset_expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $update_query = "UPDATE user SET reset_token='$reset_token', reset_expiry='$reset_expiry' WHERE email_address='$email_address'";
            if (mysqli_query($con, $update_query)) {
                $reset_link = "https://seeknfind.000.pe/reset_password.php?token=$reset_token";

                // Send reset email using PHPMailer
                $mail = new PHPMailer(true);
                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'app@gmail.com';
                    $mail->Password = '**********';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Recipients
                    $mail->setFrom('app@gmail.com', 'SeekNFind');
                    $mail->addAddress($email_address);

                    // Content
                    $mail->Subject = 'Password Reset Request';
                    $mail->Body = "To reset your password, please click the link below:\n$reset_link";

                    if ($mail->send()) {
                        $error_message = "A password reset link has been sent to your email address.";
                    } else {
                        $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                } catch (Exception $e) {
                    $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error_message = "Error: " . mysqli_error($con);
            }
        } else {
            $error_message = "No user found with that email address.";
        }
    } else {
        $error_message = "Please enter your email address.";
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
    <title>Forgot Password</title>
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
                        <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Enter your email to reset your password</h2>
                        <?php
                        if (!empty($error_message)) {
                            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                        }
                        ?>
                        <form method="post" action="forgot_password.php">
                            <div class="mb-3">
                                <label for="email_address" class="form-label">Email Address</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="basic-addon1">
                                        <i class='bx bx-envelope bx-sm'></i>
                                    </span>
                                    <input type="email" class="form-control input-group-form" id="email_address" name="email_address" required>
                                </div>
                            </div>
                            <div class="d-grid my-3">
                                <button class="btn btn_search snf_view btn-lg fw-semibold" type="submit">Request Password Reset</button>
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
