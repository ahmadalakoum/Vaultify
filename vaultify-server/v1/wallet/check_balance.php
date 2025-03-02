<?php
// require cors
require '../cors.php';
session_start();
require_once "../db_connection/connection.php";
// Check if user is logged in
require "../bearer.php";
//get the user id from the session
$userID = getBearerToken();
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['wallet_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Wallet ID is required']);
    exit();
}

$walletID = $data['wallet_id'];

// get the balance from user's wallet
$stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = :userID AND id=:walletID");

$stmt->bindParam(':walletID', $walletID);
$stmt->bindParam(':userID', $userID);

$stmt->execute();

$wallet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$wallet) {
    echo json_encode(['status' => 'error', 'message' => 'No wallet found for this user']);
    exit();
}

echo json_encode(['status' => 'success', 'wallet' => $wallet]);


