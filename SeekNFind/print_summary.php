<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Include database connection
include("connection.php");

// Fetch data from the database excluding admins and unverified users
$user_query = "
    SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) AS full_name, u.email_address, 
           li.item_id AS lost_item_id, li.name AS lost_item_name, li.date_found, li.location_found, 
           ri.item_id AS report_item_id, ri.name AS report_item_name, ri.date_lost, ri.location_lost,
           lic.claim_id AS lost_claim_id, lic.date_found AS claim_date_found, lic.location_found AS claim_location_found, lic.name AS claim_name,
           ric.claim_id AS report_claim_id, ric.date_found AS report_claim_date_found, ric.location_found AS report_claim_location_found, ric.name AS claim_name
    FROM user u
    LEFT JOIN lost_items li ON u.user_id = li.user_id
    LEFT JOIN report_items ri ON u.user_id = ri.user_id
    LEFT JOIN lost_items_claim lic ON u.user_id = lic.user_id
    LEFT JOIN report_items_claim ric ON u.user_id = ric.user_id
    WHERE u.is_admin = 0 AND u.email_verified = 1
";
$users_result = mysqli_query($con, $user_query);

// Initialize users array
$users = [];

// Fetch and process the fetched data
while ($row = mysqli_fetch_assoc($users_result)) {
    $user_id = $row['user_id'];
    if (!isset($users[$user_id])) {
        $users[$user_id] = [
            'full_name' => $row['full_name'],
            'email_address' => $row['email_address'],
            'lost_items' => [],
            'report_items' => [],
            'claimed_items' => []
        ];
    }

    if ($row['lost_item_id']) {
        $users[$user_id]['lost_items'][] = [
            'id' => $row['lost_item_id'],
            'name' => $row['lost_item_name'],
            'date_found' => $row['date_found'],
            'location_found' => $row['location_found']
        ];
    }

    if ($row['report_item_id']) {
        $users[$user_id]['report_items'][] = [
            'id' => $row['report_item_id'],
            'name' => $row['report_item_name'],
            'date_lost' => $row['date_lost'],
            'location_lost' => $row['location_lost']
        ];
    }

    if ($row['lost_claim_id']) {
        $users[$user_id]['claimed_items'][] = [
            'claim_id' => $row['lost_claim_id'],
            'date_found' => $row['claim_date_found'],
            'name' => $row['claim_name'],
            'location_found' => $row['claim_location_found']
        ];
    }

    if ($row['report_claim_id']) {
        $users[$user_id]['claimed_items'][] = [
            'claim_id' => $row['report_claim_id'],
            'date_found' => $row['report_claim_date_found'],
            'name' => $row['claim_name'],
            'location_found' => $row['report_claim_location_found']
        ];
    }
}

// Function to compare full names for sorting
function compare_names($a, $b) {
    return strcmp($a['full_name'], $b['full_name']);
}

// Sort users array by full_name
usort($users, 'compare_names');

// Now $users is sorted alphabetically by full_name
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
    <title>Print Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
    <style>
        @media print {
            .no-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
                font-size: 12px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px;
            }
            th, td {
                word-wrap: break-word;
                max-width: 100px; /* Adjust this as needed */
                padding: 8px;
                text-align: left;
            }
            @page {
                margin: 0;
                margin-bottom: 10px;
                margin-top: 25px;
            }
            @page :nth(2) {
                margin-top: 20mm; /* Adjust this value to set the desired top margin */
            }
            body {
                -webkit-print-color-adjust: exact;
            }
            footer {
                display: none;
            }
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
<div class="mt-5 ms-3 me-3">
<div class="d-flex justify-content-center align-items-center">
            <img class="mb-2 me-1" src="img/logosnf.svg" alt="Logo" width="50px">
            <h1 class="fw-bold">SeekNFind</h1>
        </div>
        <center> <h1>Summary of Records</h1>
    <button class="btn snf_view no-print" onclick="window.print()">Print Summary</button>
    </center>
    <table class="table table-bordered table-striped mt-4">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email Address</th>
                <th>Lost Items</th>
                <th>Missing Items</th>
                <th>Claimed Items</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($user['email_address']); ?></td>
                    <td>
                        <?php if (!empty($user['lost_items'])): ?>
                            <ul>
                                <?php foreach ($user['lost_items'] as $item): ?>
                                    <li><?php echo 'Name: ' . htmlspecialchars($item['name']) . '<br>Date Found: ' . htmlspecialchars($item['date_found']) . '<br>Location Found: ' . htmlspecialchars($item['location_found']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($user['report_items'])): ?>
                            <ul>
                                <?php foreach ($user['report_items'] as $item): ?>
                                    <li><?php echo 'Name: ' . htmlspecialchars($item['name']) . '<br>Date Lost: ' . htmlspecialchars($item['date_lost']) . '<br>Location Lost: ' . htmlspecialchars($item['location_lost']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($user['claimed_items'])): ?>
                            <ul>
                                <?php foreach ($user['claimed_items'] as $item): ?>
                                    <li><?php echo 'Name: ' . htmlspecialchars($item['name']) . '<br>Date Found: ' . htmlspecialchars($item['date_found']) . '<br>Location Found: ' . htmlspecialchars($item['location_found']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
