<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $claim_id = $_POST['claim_id'];
    $item_id = $_POST['item_id'];
    $user_id = $_POST['user_id'];
    $email_address = $_POST['email_address'];
    $submitter_name = $_POST['submitter_name'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $picture_path = $_POST['picture_path'];
    $date_found = $_POST['date_found'];
    $time_found = $_POST['time_found'];
    $location_found = $_POST['location_found'];
    $item_type = $_POST['item_type'];

    $query = "UPDATE report_items_claim SET 
                item_id='$item_id', 
                user_id='$user_id', 
                email_address='$email_address', 
                submitter_name='$submitter_name', 
                name='$name', 
                description='$description', 
                picture_path='$picture_path', 
                date_found='$date_found', 
                time_found='$time_found', 
                location_found='$location_found', 
                item_type='$item_type' 
              WHERE claim_id='$claim_id'";

    if (mysqli_query($con, $query)) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
