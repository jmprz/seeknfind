<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Include database connection
include("connection.php");

// Function to send email
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

function send_email($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'app@gmail.com';
        $mail->Password = '**********';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('app@gmail.com', 'SeekNFind');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->send();
    } catch (Exception $e) {
        // Handle exception (log error, notify admin, etc.)
    }
}

function get_item_name($con, $item_id, $table) {
    $query = "SELECT name FROM $table WHERE item_id='$item_id' LIMIT 1";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $item_data = mysqli_fetch_assoc($result);
        return $item_data['name'];
    }
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_id = $_POST['item_id'];

    if (isset($_POST['approve'])) {
        // Approve item in lost_items
        $query = "UPDATE lost_items SET status='approved' WHERE item_id='$item_id'";
        mysqli_query($con, $query);
        
        // Approve item in report_items
        $query = "UPDATE report_items SET status='approved' WHERE item_id='$item_id'";
        mysqli_query($con, $query);

        $item_name_lost = get_item_name($con, $item_id, 'lost_items');
        $item_name_report = get_item_name($con, $item_id, 'report_items');

        // Send email notification for lost_items
        $query = "SELECT user.email_address FROM user 
                  JOIN lost_items ON user.user_id = lost_items.user_id
                  WHERE lost_items.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_lost", 'Good Day! <br> <br> Your found item has been approved. We are thankful for your cooperation in providing detailed information about the item, which can help us verify its ownership. If you have any additional questions or concerns, please do not hesitate to contact us. <br> <br>Sincerely,<br> SeekNFind Team');
        }

        // Send email notification for report_items
        $query = "SELECT user.email_address FROM user 
                  JOIN report_items ON user.user_id = report_items.user_id
                  WHERE report_items.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_report", 'Good Day!<br> <br>Your missing item has been approved. <br> Thank you for reaching out to us and for your patience throughout this process. We will be in touch with updates on the status of your missing item. If you have any additional questions or concerns, please do not hesitate to contact us. <br><br> Sincerely,<br> SeekNFind Team');
        }
    } elseif (isset($_POST['complete'])) {
        // Complete item in lost_items
        $query = "UPDATE lost_items SET status='completed' WHERE item_id='$item_id'";
        mysqli_query($con, $query);
        
        // Complete item in report_items
        $query = "UPDATE report_items SET status='completed' WHERE item_id='$item_id'";
        mysqli_query($con, $query);

        $item_name_lost = get_item_name($con, $item_id, 'lost_items');
        $item_name_report = get_item_name($con, $item_id, 'report_items');

        // Send email notification for lost_items
        $query = "SELECT user.email_address FROM user 
                  JOIN lost_items ON user.user_id = lost_items.user_id
                  WHERE lost_items.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_lost", 'Good Day! <br><br>Your found item has been returned to its rightful owner. Thank you for bringing this to our attention and for your cooperation in the process. We appreciate your help in reuniting the items with their owners. If you have any further questions or concerns, please do not hesitate to reach out to us. <br><br> Sincerely,<br> SeekNFind Team');
        }

        // Send email notification for report_items
        $query = "SELECT user.email_address FROM user 
                  JOIN report_items ON user.user_id = report_items.user_id
                  WHERE report_items.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_report", 'Good Day!<br> <br>We hope this experience has been a positive one for you and that you continue to use our services in the future. Thank you for your patience and understanding throughout this process. We strive to provide efficient and reliable assistance to all of our users. If you have any further questions or concerns, please do not hesitate to reach out to us.<br> <br> Sincerely, <br> SeekNFind Team');
        }
    } elseif (isset($_POST['delete'])) {
        // Delete item in lost_items
        $query = "DELETE FROM lost_items WHERE item_id='$item_id'";
        mysqli_query($con, $query);
        
        // Delete item in report_items
        $query = "DELETE FROM report_items WHERE item_id='$item_id'";
        mysqli_query($con, $query);

        $item_name_lost = get_item_name($con, $item_id, 'lost_items');
        $item_name_report = get_item_name($con, $item_id, 'report_items');

        // Send email notification for lost_items
        $query = "SELECT user.email_address FROM user 
                  JOIN lost_items ON user.user_id = lost_items.user_id
                  WHERE lost_items.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_lost", 'Good Day! <br><br>Your found item has been deleted. If you have any further questions or concerns, please do not hesitate to reach out to us. <br><br> Sincerely, <br> SeekNFind Team');
        }

        // Send email notification for report_items
        $query = "SELECT user.email_address FROM user 
                  JOIN report_items ON user.user_id = report_items.user_id
                  WHERE report_items.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_report", 'Good Day! <br><br>Your missing item has been deleted. If you have any further questions or concerns, please do not hesitate to reach out to us. <br><br> Sincerely, <br> SeekNFind Team');
        }
    } elseif (isset($_POST['approve_claim'])) {
        // Approve claim in lost_items_claim
        $query = "UPDATE lost_items_claim SET claim_status='approved' WHERE item_id='$item_id'";
        mysqli_query($con, $query);
        
        // Approve claim in report_items_claim
        $query = "UPDATE report_items_claim SET claim_status='approved' WHERE item_id='$item_id'";
        mysqli_query($con, $query);

        $item_name_lost = get_item_name($con, $item_id, 'lost_items_claim');
        $item_name_report = get_item_name($con, $item_id, 'report_items_claim');

        // Send email notification for lost_items_claim
        $query = "SELECT user.email_address FROM user 
                  JOIN lost_items_claim ON user.user_id = lost_items_claim.user_id
                  WHERE lost_items_claim.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_lost", 'Good Day!<br> <br>Your proof of ownership has been verified. Thank you for choosing SeekNFind for your lost item recovery needs.  We appreciate your cooperation and look forward to assisting you again in the future. If you have any further questions or concerns, please do not hesitate to reach out to us. <br><br> Sincerely, <br> SeekNFind Team');
        }

        // Send email notification for report_items_claim
        $query = "SELECT user.email_address FROM user 
                  JOIN report_items_claim ON user.user_id = report_items_claim.user_id
                  WHERE report_items_claim.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_report", 'Good Day! <br><br>Your proof of finding the missing item has been verified. We appreciate your cooperation, and the item has been successfully returned to the owner. If you have any further questions or concerns, please do not hesitate to reach out to us. <br><br> Sincerely, <br> SeekNFind Team');
        }
    } elseif (isset($_POST['reject_claim'])) {
        // Reject claim in lost_items_claim
        $query = "UPDATE lost_items_claim SET claim_status='rejected' WHERE item_id='$item_id'";
        mysqli_query($con, $query);
        
        // Reject claim in report_items_claim
        $query = "UPDATE report_items_claim SET claim_status='rejected' WHERE item_id='$item_id'";
        mysqli_query($con, $query);

        $item_name_lost = get_item_name($con, $item_id, 'lost_items_claim');
        $item_name_report = get_item_name($con, $item_id, 'report_items_claim');

        // Send email notification for lost_items_claim
        $query = "SELECT user.email_address FROM user 
                  JOIN lost_items_claim ON user.user_id = lost_items_claim.user_id
                  WHERE lost_items_claim.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_lost", 'Good Day! <br><br>Your proof of ownership has been rejected. If you have any further questions or concerns, please do not hesitate to reach out to us. <br><br> Sincerely, <br> SeekNFind Team');
        }

        // Send email notification for report_items_claim
        $query = "SELECT user.email_address FROM user 
                  JOIN report_items_claim ON user.user_id = report_items_claim.user_id
                  WHERE report_items_claim.item_id='$item_id'";
        $result = mysqli_query($con, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $user_data = mysqli_fetch_assoc($result);
            send_email($user_data['email_address'], "$item_name_report", 'Good Day!<br> <br>Your proof of finding the missing item has been rejected. If you have any further questions or concerns, please do not hesitate to reach out to us.<br> <br> Sincerely, <br> SeekNFind Team');
        }
    }
}

header("Location: admin_dashboard.php");
exit;
?>
