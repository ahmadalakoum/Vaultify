<?php

session_start();
require_once "../db_connection/connection.php";
// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
// get the balance from user's wallet
$stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = :userID");

$stmt->bindParam(':userID', $userID);

$stmt->execute();

$wallet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$wallet) {
    echo json_encode(['status' => 'error', 'message' => 'No wallet found for this user']);
    exit();
}

echo json_encode(['status' => 'success', 'wallet' => $wallet]);


