<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

include("connection.php");
$pending_items = mysqli_query($con, "SELECT * FROM lost_items WHERE status='pending' UNION SELECT * FROM report_items WHERE status='pending'");
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
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
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
            <a class="nav-link snf_view" href="admin_dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link snf_view_active" href="admin_pending.php">Pending Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link snf_view" href="admin_tracking.php">Tracking Items</a>
        </li>
        <li class="nav-item">
            <a class="nav-link snf_view" href="admin_completed.php">Completed Items</a>
        </li>
    </ul>
</div>
<div class="container mt-5">
    <h1>Pending Items</h1>
    <div class="row">
        <?php while ($row = mysqli_fetch_assoc($pending_items)): ?>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-img-container">
                        <img src="<?php echo htmlspecialchars($row['picture_path']); ?>" class="img-fluid img-thumbnail" alt="User Item">
                    </div>
                    <div class="card-body">
                        <h5 class="card-title fw-semibold"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <h5><i class='bx bx-pencil me-2'></i>Description</h5>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <h5><i class='bx bx-calendar me-2'></i>Date</h5>
                        <p><?php echo htmlspecialchars($row['date_found']); ?></p>
                        <h5><i class='bx bx-map me-2'></i>Location</h5>
                        <p><?php echo htmlspecialchars($row['location_found']); ?></p>
                        <form action="admin_actions.php" method="post">
                            <input type="hidden" name="item_id" value="<?php echo $row['item_id']; ?>">
                            <button class="btn snf_view" type="submit" name="approve" class="btn btn-success">Approve</button>
                            <button class="btn snf_report" type="submit" name="delete" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
