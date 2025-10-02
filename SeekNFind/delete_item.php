<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Include database connection
include("connection.php");

// Check if item_id and table_name are set in POST request
if (isset($_POST['item_id']) && isset($_POST['lost_items'])) {
    $item_id = mysqli_real_escape_string($con, $_POST['item_id']);
    $lost_items = mysqli_real_escape_string($con, $_POST['lost_items']);

    // Delete the item from the specified table
    $delete_query = "DELETE FROM $lost_items WHERE item_id='$item_id'";
    if (mysqli_query($con, $delete_query)) {
        $_SESSION['message'] = "Item deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting item: " . mysqli_error($con);
    }
} elseif (isset($_POST['item_id']) && isset($_POST['report_items'])) {
    $item_id = mysqli_real_escape_string($con, $_POST['item_id']);
    $report_items = mysqli_real_escape_string($con, $_POST['report_items']);

    // Delete the claim from the specified table
    $delete_query = "DELETE FROM $report_items WHERE item_id='$item_id'";
    if (mysqli_query($con, $delete_query)) {
        $_SESSION['message'] = "Item deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting item: " . mysqli_error($con);
    }
} elseif (isset($_POST['claim_id']) && isset($_POST['report_items_claim'])) {
    $claim_id = mysqli_real_escape_string($con, $_POST['claim_id']);
    $report_items_claim = mysqli_real_escape_string($con, $_POST['report_items_claim']);

    // Delete the claim from the specified table
    $delete_query = "DELETE FROM $report_items_claim WHERE claim_id='$claim_id'";
    if (mysqli_query($con, $delete_query)) {
        $_SESSION['message'] = "Claim deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting claim: " . mysqli_error($con);
    }
} elseif (isset($_POST['claim_id']) && isset($_POST['lost_items_claim'])) {
    $claim_id = mysqli_real_escape_string($con, $_POST['claim_id']);
    $lost_items_claim = mysqli_real_escape_string($con, $_POST['lost_items_claim']);

    // Delete the claim from the specified table
    $delete_query = "DELETE FROM $lost_items_claim WHERE claim_id='$claim_id'";
    if (mysqli_query($con, $delete_query)) {
        $_SESSION['message'] = "Claim deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting claim: " . mysqli_error($con);
    }
} elseif (isset($_POST['user_id']) && isset($_POST['user'])) {
    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $user = mysqli_real_escape_string($con, $_POST['user']);

    // Delete the user from the specified table
    $delete_query = "DELETE FROM $user WHERE user_id='$user_id'";
    if (mysqli_query($con, $delete_query)) {
        $_SESSION['message'] = "User deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting user: " . mysqli_error($con);
    }
} else {
    $_SESSION['message'] = "Invalid request.";
}

// Redirect back to admin dashboard
header("Location: admin_dashboard.php");
exit;
?>
