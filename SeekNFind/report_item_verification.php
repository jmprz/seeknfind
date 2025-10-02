<?php
session_start();
include("connection.php");
include("functions.php");
require 'vendor/autoload.php'; // Include Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch user information
    $query = "SELECT CONCAT(first_name, ' ', last_name) AS submitter_name, email_address FROM user WHERE user_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        $submitter_name = $row['submitter_name'];
        $email_address = $row['email_address'];
    } else {
        echo "Error: User information not found.";
        exit;
    }

    // Get POST variables
    $description = $_POST['description'] ?? '';
    $date_found = $_POST['date_found'] ?? '';
    $time_found = $_POST['time_found'] ?? '';
    $location_found = $_POST['location_found'] ?? '';
    $item_id = $_POST['item_id'] ?? '';

    // Fetch item name from lost_items table
    $query = "SELECT name FROM report_items WHERE item_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row) {
        $item_name = $row['name'];
    } else {
        echo "Error: Item information not found.";
        exit;
    }

    // Handle file upload
    $targetDirectory = "uploads/claims/";
    $originalFileName = basename($_FILES["verification_file"]["name"]);
    $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
    $newFileName = $user_id . '_' . $originalFileName;
    $targetFilePath = $targetDirectory . $newFileName;

    if (move_uploaded_file($_FILES["verification_file"]["tmp_name"], $targetFilePath)) {
        // Prepare and bind
        $query = "INSERT INTO report_items_claim (item_id, user_id, submitter_name, email_address, name, description, date_found, time_found, location_found, picture_path, claim_status, claim_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'report')";
        $stmt = $con->prepare($query);
        
        if ($stmt) {
            $stmt->bind_param("iissssssss", $item_id, $user_id, $submitter_name, $email_address, $item_name, $description, $date_found, $time_found, $location_found, $targetFilePath);
            
            if ($stmt->execute()) {
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
                    $mail->Subject = 'Verification';
                    $mail->Body = "Thank you for providing proof of finding the missing item. Please go to the Lost and Found Room to continue the verification process. Always check your Email for any updates on the status of the item. If you have any further questions or concerns, please do not hesitate to reach out to us.";

                    $mail->send();
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }

                header("Location: index.php");
                exit;
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $con->error;
        }
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
} else {
    echo "Please enter some valid information.";
}
?>
