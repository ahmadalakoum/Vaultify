<?php
session_start();
require_once "../../db_connection/connection.php";

// Check if admin is logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get request data
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['document_id']) || empty($data['status'])) {
    echo json_encode(['status' => 'error', 'message' => 'Document ID and status are required']);
    exit();
}

$documentID = intval($data['document_id']);
$status = $data['status']; // 'approved' or 'rejected'

// Validate status
if (!in_array($status, ['approved', 'rejected'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
    exit();
}

// Fetch user ID from document
$stmt = $pdo->prepare("SELECT user_id FROM documents WHERE id = :documentID AND status = 'pending'");
$stmt->bindParam(':documentID', $documentID);
$stmt->execute();
$document = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$document) {
    echo json_encode(['status' => 'error', 'message' => 'Document not found or already processed']);
    exit();
}

$userID = $document['user_id'];

// Update document status
$stmt = $pdo->prepare("UPDATE documents SET status = :status WHERE id = :documentID");
$stmt->bindParam(':status', $status);
$stmt->bindParam(':documentID', $documentID);
$stmt->execute();

// If approved, update user verification status & increase transaction limits
if ($status === 'approved') {
    $newLimit = 5000; // Increase the user's daily limit
    $stmt = $pdo->prepare("UPDATE users SET verification_status = 'verified', daily_limit = :newLimit WHERE id = :userID");
    $stmt->bindParam(':newLimit', $newLimit);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
}

echo json_encode(['status' => 'success', 'message' => 'Document status updated successfully']);
exit();
?>