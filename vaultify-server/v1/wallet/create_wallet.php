<?php
session_start();
require_once "../db_connection/connection.php";
// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['wallet_name'])) {
    echo json_encode(['status' => 'error', 'message' => 'Wallet name is required']);
    exit();
}
$walletName = $data['wallet_name'];
$currency = !empty($data['currency']) ? strtoupper($data['currency']) : 'USD';
// Check if a wallet with the same name already exists for the user
$stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = :userID AND wallet_name = :walletName");
$stmt->execute(['userID' => $userID, 'walletName' => $walletName]);
$existingWallet = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existingWallet) {
    echo json_encode(['status' => 'error', 'message' => 'Wallet with this name already exists']);
    exit();
}
// Create a new wallet
$stmt = $pdo->prepare("INSERT INTO wallets (user_id, wallet_name, balance,currency) VALUES (:userID, :walletName, 0.00,:currency)");
$stmt->execute(['userID' => $userID, 'walletName' => $walletName, 'currency' => $currency]);

echo json_encode(['status' => 'success', 'message' => 'Wallet created successfully']);
exit();
