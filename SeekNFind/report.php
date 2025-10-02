<?php
session_start();

include("connection.php");
include("functions.php");
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the user ID
$user_id = $_SESSION['user_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the original filename
    $originalFileName = basename($_FILES["file"]["name"]);

    // Extract the file extension
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

    // Generate the new filename by concatenating the user ID and the original filename
    $newFileName = $user_id . '_report_' . $originalFileName;

    // File upload directory
    $targetDirectory = "uploads/";

    // Set the target path with the new filename
    $targetFilePath = $targetDirectory . $newFileName;

    // Move the uploaded file to the target directory with the new filename
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
        // File uploaded successfully

        // Retrieve form data
        $name = $_POST["name"];
        $description = $_POST["description"];
        $dateLost = $_POST["date_lost"];
        $timeLost = $_POST["time_lost"];
        $location = $_POST["location_lost"];

        if (!empty($name) && !empty($description) && !empty($dateLost) && !empty($timeLost)) {
            // Fetch submitter name and email from user table
            $query = "SELECT CONCAT(first_name, ' ', last_name) AS submitter_name, email_address FROM user WHERE user_id = ?";
            $stmt = $con->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $submitter_name = $row['submitter_name'];
            $email_address = $row['email_address'];

            // Save to database with 'pending' status
            $query = "INSERT INTO report_items (user_id, submitter_name, name, description, date_lost, time_lost, location_lost, picture_path, item_type, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'lost', 'pending')";
            $stmt = $con->prepare($query);
            $stmt->bind_param("isssssss", $user_id, $submitter_name, $name, $description, $dateLost, $timeLost, $location, $targetFilePath);
            $stmt->execute();

            // Send email notification
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
                $mail->Subject = 'Your Item is On Process';
                $mail->Body = "Good Day! Thank you for reporting a missing item. Always check your Email for any updates on the status of your missing item. If you have any further questions or concerns, please do not hesitate to reach out to us.";

                $mail->send();
            } catch (Exception $e) {
                // Handle email error
            }

            header("Location: index.php");
            exit;
        } else {
            echo "Please enter some valid information";
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
?>
