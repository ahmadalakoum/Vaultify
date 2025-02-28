<?php
session_start();
require_once "../../db_connection/connection.php";

// Check if admin is logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get POST data (Ticket ID, Response)
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['ticket_id']) || empty($data['response'])) {
    echo json_encode(['status' => 'error', 'message' => 'Ticket ID and response are required']);
    exit();
}

$ticketID = intval($data['ticket_id']);
$response = $data['response'];
$adminID = $_SESSION['userID'];

// Insert the admin's response
$stmt = $pdo->prepare("INSERT INTO ticket_responses (ticket_id, admin_id, response) 
                       VALUES (:ticketID, :adminID, :response)");
$stmt->bindParam(':ticketID', $ticketID);
$stmt->bindParam(':adminID', $adminID);
$stmt->bindParam(':response', $response);
$stmt->execute();

// Update ticket status to 'in_progress' if it wasn't already closed
$stmt = $pdo->prepare("UPDATE support_tickets SET status = 'closed' WHERE id = :ticketID AND status = 'open'");
$stmt->bindParam(':ticketID', $ticketID);
$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'Response submitted successfully']);
exit();
?>