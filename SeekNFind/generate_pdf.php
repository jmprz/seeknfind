<?php
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

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

// Process the fetched data to group items by user
$users = [];
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

// Generate PDF using mPDF
$mpdf = new \Mpdf\Mpdf();
$html = '<h1>Summary of Records</h1>';
$html .= '<table border="1">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Lost Items</th>
                    <th>Missing Items</th>
                    <th>Claimed Items</th>
                </tr>
            </thead>
            <tbody>';

foreach ($users as $user) {
    $html .= '<tr>
                <td>' . htmlspecialchars($user['full_name']) . '</td>
                <td>' . htmlspecialchars($user['email_address']) . '</td>
                <td>';
    if (!empty($user['lost_items'])) {
        $html .= '<ul>';
        foreach ($user['lost_items'] as $item) {
            $html .= '<li>' . 'Name: ' . htmlspecialchars($item['name']) . '<br>Date Found: ' . htmlspecialchars($item['date_found']) . '<br>Location Found: ' . htmlspecialchars($item['location_found']) . '</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= 'N/A';
    }
    $html .= '</td><td>';
    if (!empty($user['report_items'])) {
        $html .= '<ul>';
        foreach ($user['report_items'] as $item) {
            $html .= '<li>' . 'Name: ' . htmlspecialchars($item['name']) . '<br>Date Lost: ' . htmlspecialchars($item['date_lost']) . '<br>Location Lost: ' . htmlspecialchars($item['location_lost']) . '</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= 'N/A';
    }
    $html .= '</td><td>';
    if (!empty($user['claimed_items'])) {
        $html .= '<ul>';
        foreach ($user['claimed_items'] as $item) {
            $html .= '<li>' . 'Name: ' . htmlspecialchars($item['name']) . '<br>Date Found: ' . htmlspecialchars($item['date_found']) . '<br>Location Found: ' . htmlspecialchars($item['location_found']) . '</li>';
        }
        $html .= '</ul>';
    } else {
        $html .= 'N/A';
    }
    $html .= '</td></tr>';
}
$html .= '</tbody></table>';

$mpdf->WriteHTML($html);
$mpdf->Output('summary.pdf', 'D');
?>