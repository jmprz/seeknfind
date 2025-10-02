<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE users SET 
                username='$username', 
                email='$email', 
                role='$role' 
              WHERE user_id='$user_id'";

    if (mysqli_query($con, $query)) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
