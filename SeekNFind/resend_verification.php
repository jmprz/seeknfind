<?php
include("connection.php");
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error_message = "";

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Retrieve the user's email and verification code from the database
    $query = "SELECT email_address, verification_code FROM user WHERE user_id = '$user_id' LIMIT 1";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user_data = mysqli_fetch_assoc($result);
        $email_address = $user_data['email_address'];
        $verification_code = $user_data['verification_code'];

        // Send verification email using PHPMailer
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true;
            $mail->Username = 'app@gmail.com'; // SMTP username
            $mail->Password = '**********'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = 587; // TCP port to connect to

            // Recipients
            $mail->setFrom('app@gmail.com', 'SeekNFind');
            $mail->addAddress($email_address); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Email Verification';
            $mail->Body = "Your verification code is: <b>$verification_code</b><br>Please enter this code on the website to verify your email address.";

            if ($mail->send()) {
                $message = "Verification code resent successfully!";
            } else {
                $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } catch (Exception $e) {
            $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $error_message = "User not found or already verified.";
    }
} else {
    $error_message = "Invalid request.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Resend Verification Code</title>
</head>
<body>
<section class="py-3 py-md-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-6 col-xxl-5">
                <div class="card border border-light-subtle rounded-4 snf_shadow">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="text-center mb-3">
                            <h1 class="fw-bold">Resend Verification Code</h1>
                        </div>
                        <?php
                        if (!empty($error_message)) {
                            echo '<div class="alert alert-danger" role="alert">' . $error_message . '</div>';
                        } elseif (isset($message)) {
                            echo '<div class="alert alert-success" role="alert">' . $message . '</div>';
                        }
                        ?>
                        <div class="text-center">
                            <a href="verify_email.php" class="text-decoration-none" style="color: #526afeff;">Go back to verification page</a>
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
