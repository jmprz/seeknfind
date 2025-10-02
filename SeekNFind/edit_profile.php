<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $home_address = $_POST['home_address'];
    $profile_image = $_FILES['profile_image']['name'];
    
    // Handle image upload
    if (!empty($profile_image)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_image);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['profile_image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                // Update the profile image in the database
                $query = "UPDATE user SET profile='$target_file' WHERE user_id='$user_id'";
                mysqli_query($con, $query);
            }
        }
    }

    // Update user details in the database
    $query = "UPDATE user SET first_name='$first_name', last_name='$last_name', home_address='$home_address' WHERE user_id='$user_id'";
    if (mysqli_query($con, $query)) {
        header("Location: profile.php");
        die;
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>
