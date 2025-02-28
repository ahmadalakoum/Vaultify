<?php
session_start();
require_once "../../db_connection/connection.php";

// Check if admin is logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Fetch all pending documents
$stmt = $pdo->query("SELECT d.id, d.user_id, u.email, d.file_path, d.status, d.uploaded_at
                     FROM documents d
                     JOIN users u ON d.user_id = u.id
                     WHERE d.status = 'pending'");
$documents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return response
echo json_encode(['status' => 'success', 'documents' => $documents]);
exit();
?>