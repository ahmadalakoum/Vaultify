<?php
// require cors
require '../cors.php';
session_start();
require_once "../db_connection/connection.php";

// Check if user is logged in
require "../bearer.php";
//get the user id from the session
$userID = getBearerToken();

// Fetch user's tickets
$stmt = $pdo->prepare("SELECT * FROM support_tickets WHERE user_id = :userID ORDER BY created_at DESC");
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$tickets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return response
echo json_encode(['status' => 'success', 'tickets' => $tickets]);
exit();
?>