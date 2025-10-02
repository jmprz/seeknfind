<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $item_id = $_POST['item_id'];
    $user_id = $_POST['user_id'];
    $submitter_name = $_POST['submitter_name'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $picture_path = $_POST['picture_path'];
    $date_lost = $_POST['date_lost'];
    $time_lost = $_POST['time_lost'];
    $location_lost = $_POST['location_lost'];
    $status = $_POST['status'];
    $item_type = $_POST['item_type'];

    $query = "UPDATE report_items SET 
                user_id='$user_id', 
                submitter_name='$submitter_name', 
                name='$name', 
                description='$description', 
                picture_path='$picture_path', 
                date_lost='$date_lost', 
                time_lost='$time_lost', 
                location_lost='$location_lost', 
                    status='$status',
                item_type='$item_type' 
              WHERE item_id='$item_id'";

    if (mysqli_query($con, $query)) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
