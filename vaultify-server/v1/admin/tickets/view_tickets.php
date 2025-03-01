<?php
require "../../cors.php";
session_start();
require_once "../../db_connection/connection.php";

// Check if admin is logged in
if (!isset($_SESSION['userID']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Fetch all tickets
$stmt = $pdo->query("SELECT t.id, t.user_id, u.email, t.subject,t.description, t.status, t.created_at
                     FROM support_tickets t
                     JOIN users u ON t.user_id = u.id
                     ORDER BY t.created_at DESC");
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return response
echo json_encode(['status' => 'success', 'tickets' => $tickets]);
exit();
?>