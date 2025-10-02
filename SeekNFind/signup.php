<?php
session_start();

include("connection.php");
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to check if user is logged in
if (!function_exists('check_login')) {
    function check_login($con)
    {
        if (isset($_SESSION['user_id'])) {
            $id = $_SESSION['user_id'];
            $query = "select * from user where user_id = '$id' limit 1";

            $result = mysqli_query($con, $query);
            if ($result && mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);
                return $user_data;
            }
        }

        // Redirect to login if not logged in
        header("Location: login.php");
        die;
    }
}

// Function to generate random numbers
if (!function_exists('random_num')) {
    function random_num($length)
    {
        $text = "";
        if ($length < 5) {
            $length = 5;
        }

        $len = rand(4, $length);

        for ($i = 0; $i < $len; $i++) {
            $text .= rand(0, 9);
        }
        return $text;
    }
}

// Function to handle registration process
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $account_type = trim($_POST['account_type']);
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $home_address = trim($_POST['home_address']);
    $email_address = trim($_POST['email_address']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $program = isset($_POST['program']) ? trim($_POST['program']) : '';
    $year_section = isset($_POST['year_section']) ? trim($_POST['year_section']) : '';

    // Validate CAPTCHA response
    $secret = "6Ld1ogkqAAAAAEcqvPqlNYvnWspBGZ4hm-O4DNDA";
    $response = $_POST["g-recaptcha-response"];
    $remoteip = $_SERVER["REMOTE_ADDR"];

    $url = "https://www.google.com/recaptcha/api/siteverify";
    $data = [
        'secret' => $secret,
        'response' => $response,
        'remoteip' => $remoteip
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify);

    if ($captcha_success->success) {
        // CAPTCHA validation successful, proceed with registration

        // Validate form fields
        if ($password !== $confirm_password) {
            $error_message = "Passwords do not match. Please try again.";
        } elseif (!empty($account_type) && !empty($first_name) && !empty($last_name) && !empty($home_address) && !empty($email_address) && !empty($password) && !is_numeric($account_type)) {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Generate a random user ID
            $user_id = random_num(10);

            // Set default profile picture
            $default_profile_picture = 'img/default_user.png';

            // Generate 6-character verification code
            $verification_code = substr(md5(uniqid("your_random_string", true)), 0, 6);

            // Insert user data into the database using prepared statements
            $stmt = $con->prepare("INSERT INTO user (user_id, account_type, first_name, last_name, home_address, email_address, password, profile_picture, program, year_section, email_verified, verification_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)");
            $stmt->bind_param("issssssssss", $user_id, $account_type, $first_name, $last_name, $home_address, $email_address, $hashed_password, $default_profile_picture, $program, $year_section, $verification_code);

            if ($stmt->execute()) {
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
                    $mail->Subject = 'Email Verification';
                    $mail->Body = "Good Day! Your verification code is: $verification_code";

                    if ($mail->send()) {
                        // Redirect to verification page with email parameter
                        header("Location: verify_email.php?email=" . urlencode($email_address));
                        exit();
                    } else {
                        $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                    }
                } catch (Exception $e) {
                    $error_message = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $error_message = "Error: " . $stmt->error;
            }
        } else {
            $error_message = "Please enter some valid information";
        }
    } else {
        // CAPTCHA validation failed, handle error
        $error_message = "reCAPTCHA verification failed. Please try again.";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>SeekNFind | Sign Up</title>
    <style>
@media (max-width: 576px) {
html body {
background-color: white;
}
}
        .password-toggle {
            position: relative;
        }
        .password-toggle .form-control {
            padding-right: 2.5rem;
        }
        .password-toggle .toggle-icon {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
        }
    </style>
</head>
<body>
<section class="py-3 py-md-5">
    <div class="container">
        <div class="row justify-content-center align-items-center">
            <div class="col-12 col-sm-10 col-md-9 col-lg-6 col-xl-6 col-xxl-6">
                <div class="card card_login border border-light-subtle rounded-4 snf_shadow">
                    <div class="card-body p-3 p-md-4 p-xl-5">
                        <div class="text-center mb-3">
                            <img src="img/logosnf.svg" alt="SeekNFind Logo" width="70">
                            <h1 class="fw-bold">SeekNFind</h1>
                        </div>
                        <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Create an account</h2>
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
                                            <i class='bx bx-list-ul bx-sm'></i>
                                        </span>
                                        <select class="form-select input-group-form" name="account_type" id="account_type" onchange="toggleFields()" required>
                                            <option selected disabled>Select account type</option>
                                            <option value="Student">Student</option>
                                            <option value="Faculty">Faculty</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class='bx bx-user bx-sm'></i>
                                        </span>
                                        <input type="text" class="form-control input-group-form" name="first_name" placeholder="First Name" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class='bx bx-user bx-sm'></i>
                                        </span>
                                        <input type="text" class="form-control input-group-form" name="last_name" placeholder="Last Name" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class='bx bx-home-alt bx-sm'></i>
                                        </span>
                                        <input type="text" class="form-control input-group-form" name="home_address" placeholder="Home Address" required>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon1">
                                            <i class='bx bx-envelope bx-sm'></i>
                                        </span>
                                        <input type="email" class="form-control input-group-form" name="email_address" placeholder="Email" required>
                                    </div>
                                </div>
                                <div id="studentFields" class="d-none">
                                    <div class="row gy-2 overflow-hidden">
                                        <div class="col-12">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">
                                                <i class="ri-graduation-cap-line" style="font-size: 1.4rem;"></i>
                                                </span>
                                                <select class="form-select input-group-form" name="program" id="program" onchange="toggleFields()" required>
                                                    <option selected disabled>Select Program</option>
                                                    <option value="BSCS">Bachelor of Science in Computer Science</option>
                                                    <option value="BSIT">Bachelor of Science in Information Technology</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="input-group mb-3">
                                                <span class="input-group-text" id="basic-addon1">
                                                <i class="ri-graduation-cap-line" style="font-size: 1.4rem;"></i>
                                                </span>
                                                <input type="text" class="form-control input-group-form" name="year_section" id="year_section" placeholder="Year & Section">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 password-toggle">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon2">
                                            <i class='bx bx-lock-alt bx-sm'></i>
                                        </span>
                                        <input type="password" class="form-control input-group-form" name="password" id="password" placeholder="Password" required>
                                        <i class='bx bx-hide bx-sm toggle-icon' onclick="togglePassword('password', this)"></i>
                                    </div>
                                </div>
                                <div class="col-12 password-toggle">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text" id="basic-addon2">
                                            <i class='bx bx-lock-alt bx-sm'></i>
                                        </span>
                                        <input type="password" class="form-control input-group-form" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required>
                                        <i class='bx bx-hide bx-sm toggle-icon' onclick="togglePassword('confirm_password', this)"></i>
                                    </div>
                                </div>
                                <div class="col-12">
                                <p style="font-size: 0.8rem;">In accordance with the Data Privacy Act of 2012, all personal information shared will be rest assured to be treated with the utmost confidentiality and privacy.  
                                </p>
                                <div class="form-check">
                                        <input class="form-check-input custom-switch" type="checkbox" name="da" id="da" required>
                                        <label class="form-check-label" for="da" style="font-size: 0.8rem;">I read and understood the notice and agree to disclose my responses, as well as my personal information.</label>
                                    </div>
                                    </div>
                                <center><div class="col-12">
                                    <div class="g-recaptcha" data-sitekey="6Ld1ogkqAAAAAJSfT1BYZ7B7H-1oFJej1xzkBUHD"></div>
                                </div>
                                </center>
                                <div class="col-12">
                                    <div class="d-grid my-3">
                                        <button class="btn btn_search snf_view btn-lg fw-semibold" type="submit">Sign up</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="m-0 text-secondary text-center">Already have an account? <a href="login.php" class="text-decoration-none" style="color: #526afeff;">Log in Here</a></p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modal for error message -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php echo $error_message; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function toggleFields() {
    var accountType = document.getElementById("account_type").value;
    var studentFields = document.getElementById("studentFields");
    if (accountType === "Faculty") {
        studentFields.classList.add("d-none");
        document.getElementById("program").required = false;
        document.getElementById("year_section").required = false;
    } else {
        studentFields.classList.remove("d-none");
        document.getElementById("program").required = true;
        document.getElementById("year_section").required = true;
    }
}

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


function trimInputFields() {
    var inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="password"], select');
    inputs.forEach(function(input) {
        input.value = input.value.trim();
    });
}

document.addEventListener("DOMContentLoaded", function() {
    toggleFields();

    <?php
    if (!empty($error_message)) {
        echo "var errorModal = new bootstrap.Modal(document.getElementById('errorModal'), {});";
        echo "errorModal.show();";
    }
    ?>

    var form = document.querySelector('form');
    form.addEventListener('submit', trimInputFields);
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
