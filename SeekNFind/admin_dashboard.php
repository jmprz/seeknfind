<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Include database connection
include("connection.php");

// Fetch data from the database
$lost_items_query = "SELECT * FROM lost_items ORDER BY item_id ASC";
$lost_items = mysqli_query($con, $lost_items_query);

$report_items_query = "SELECT * FROM report_items ORDER BY item_id ASC";
$report_items = mysqli_query($con, $report_items_query);

$lost_items_claim_query = "SELECT * FROM lost_items_claim ORDER BY claim_id ASC";
$lost_items_claim = mysqli_query($con, $lost_items_claim_query);

$report_items_claim_query = "SELECT * FROM report_items_claim ORDER BY claim_id ASC";
$report_items_claim = mysqli_query($con, $report_items_claim_query);

$user_query = "SELECT * FROM user ORDER BY id ASC";
$users = mysqli_query($con, $user_query);
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
    <title>SeekNFind | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        table {
            font-size: 0.7rem;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img class="mb-2 me-1" src="img/logosnf.svg" alt="Logo" width="50px">
            <h1 class="fw-bold">SeekNFind</h1>
        </div>
        <a class="btn snf_view fs-5 rounded-3 ms-auto" href="logout.php" type="button">
            <i class='bx bx-log-out me-1'></i>Log Out
        </a>
    </div>
    <ul class="text-center nav nav-tabs rounded-3 gap-4">
        <li class="nav-item">
            <a class="nav-link snf_view_active" href="admin_dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link snf_view" href="admin_pending.php">Pending Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link snf_view" href="admin_tracking.php">Tracking Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link snf_view" href="admin_completed.php">Completed Items</a>
        </li>
    </ul>
    <div class="d-flex justify-content-between align-items-center mt-3">
    <h2 class="mt-4 mb-2">Lost Items</h2>
    <button class="btn snf_view" onclick="printDocument()"><i class='bx bx-printer me-1'></i>Print Summary</button>
    <iframe class="d-none" id="printFrame" src="print_summary.php"></iframe>
    </div>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>User ID</th>
                <th>Submitter Name</th>
                <th>Name</th>
                <th>Description</th>
                <th>Picture Path</th>
                <th>Date Found</th>
                <th>Time Found</th>
                <th>Location Found</th>
                <th>Status</th>
                <th>Item Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($lost_items)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['submitter_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['picture_path']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['location_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                    <td>
                        <form action="edit_lost_item.php" method="post">
                            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($row['item_id']); ?>">
                            <button type="submit" class="btn snf_view btn-sm">Edit</button>
                        </form>
                        <div class="mt-2"></div>
                        <form action="delete_item.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <input type="hidden" name="table_name" value="user">
                            <button type="submit" class="btn snf_report btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2 class="mt-4">Report Items</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Item ID</th>
                <th>User ID</th>
                <th>Submitter Name</th>
                <th>Name</th>
                <th>Description</th>
                <th>Picture Path</th>
                <th>Date Lost</th>
                <th>Time Lost</th>
                <th>Location Lost</th>
                <th>Status</th>
                <th>Item Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($report_items)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['submitter_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['picture_path']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_lost']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_lost']); ?></td>
                    <td><?php echo htmlspecialchars($row['location_lost']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_type']); ?></td>
                    <td>
                        <form action="edit_report_item.php" method="post">
                            <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($row['item_id']); ?>">
                            <button type="submit" class="btn snf_view btn-sm">Edit</button>
                        </form>
                        <div class="mt-2"></div>
                        <form action="delete_item.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <input type="hidden" name="table_name" value="user">
                            <button type="submit" class="btn snf_report btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2 class="mt-4">Lost Items Claim</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Claim ID</th>
                <th>Item ID</th>
                <th>User ID</th>
                <th>Email Address</th>
                <th>Submitter Name</th>
                <th>Name</th>
                <th>Description</th>
                <th>Picture Path</th>
                <th>Date Found</th>
                <th>Time Found</th>
                <th>Location Found</th>
                <th>Claim Status</th>
                <th>Claim Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($lost_items_claim)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['claim_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['email_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['submitter_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['picture_path']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['location_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['claim_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['claim_type']); ?></td>
                    <td>
                        <form action="edit_lost_items_claim.php" method="post">
                            <input type="hidden" name="claim_id" value="<?php echo htmlspecialchars($row['claim_id']); ?>">
                            <button type="submit" class="btn snf_view btn-sm">Edit</button>
                        </form>
                        <div class="mt-2"></div>
                        <form action="delete_item.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <input type="hidden" name="table_name" value="user">
                            <button type="submit" class="btn snf_report btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2 class="mt-4">Report Items Claim</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Claim ID</th>
                <th>Item ID</th>
                <th>User ID</th>
                <th>Email Address</th>
                <th>Submitter Name</th>
                <th>Name</th>
                <th>Description</th>
                <th>Picture Path</th>
                <th>Date Found</th>
                <th>Time Found</th>
                <th>Location Found</th>
                <th>Claim Status</th>
                <th>Claim Type</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($report_items_claim)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['claim_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['item_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['email_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['submitter_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['picture_path']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['time_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['location_found']); ?></td>
                    <td><?php echo htmlspecialchars($row['claim_status']); ?></td>
                    <td><?php echo htmlspecialchars($row['claim_type']); ?></td>
                    <td>
                        <form action="edit_report_items_claim.php" method="post">
                            <input type="hidden" name="claim_id" value="<?php echo htmlspecialchars($row['claim_id']); ?>">
                            <button type="submit" class="btn snf_view btn-sm">Edit</button>
                        </form>
                        <div class="mt-2"></div>
                        <form action="delete_item.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <input type="hidden" name="table_name" value="user">
                            <button type="submit" class="btn snf_report btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2 class="mt-4">Users</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>Account Type</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Home Address</th>
                <th>Email Address</th>
                <th>Program</th>
                <th>Year Section</th>
                <th>Profile Picture</th>
                <th>Email Verified</th>
                <th>Verification Code</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['account_type']); ?></td>
                    <td><?php echo htmlspecialchars($row['first_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['last_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['home_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['email_address']); ?></td>
                    <td><?php echo htmlspecialchars($row['program']); ?></td>
                    <td><?php echo htmlspecialchars($row['year_section']); ?></td>
                    <td><?php echo htmlspecialchars($row['profile_picture']); ?></td>
                    <td><?php echo htmlspecialchars($row['email_verified']); ?></td>
                    <td><?php echo htmlspecialchars($row['verification_code']); ?></td>
                    <td>
                        <form action="edit_user.php" method="post">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <button type="submit" class="btn snf_view btn-sm">Edit</button>
                        </form>
                        <div class="mt-2"></div>
                        <form action="delete_item.php" method="post" style="display:inline;">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['user_id']); ?>">
                            <input type="hidden" name="table_name" value="user">
                            <button type="submit" class="btn snf_report btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
        function printDocument() {
            var iframe = document.getElementById('printFrame');
            iframe.contentWindow.focus();
            iframe.contentWindow.print();
        }
    </script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
