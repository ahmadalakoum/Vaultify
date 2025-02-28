<?php
session_start();
require_once "../db_connection/connection.php";

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get user ID from session
$userID = $_SESSION['userID'];

// Fetch user's tickets
$stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = :userID ORDER BY created_at DESC");
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return response
echo json_encode(['status' => 'success', 'tickets' => $tickets]);
exit();
?>