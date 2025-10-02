<?php
session_start();
include("connection.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $claim_id = $data['claim_id'];
    $status = $data['status'];
    $claim_type = $data['claim_type']; // Add claim_type to determine the table

    // Determine the table based on claim_type
    if ($claim_type == 'lost') {
        $table = 'lost_items_claim';
    } elseif ($claim_type == 'report') {
        $table = 'report_items_claim';
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid claim type.']);
        exit;
    }

    $query = "UPDATE $table SET claim_status = ? WHERE claim_id = ?";
    $stmt = $con->prepare($query);
    if ($stmt === false) {
        echo json_encode(['success' => false, 'error' => 'Failed to prepare statement.']);
        exit;
    }
    $stmt->bind_param("si", $status, $claim_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
}
?>
