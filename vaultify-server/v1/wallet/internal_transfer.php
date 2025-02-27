<?php

// Start session
session_start();

// Require DB connection
require_once "../db_connection/connection.php";
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }
    // Get user ID from session (if user is logged in)
    $userID = $_SESSION['userID'];

    // Get POST data (Deposit Amount)
    $data = json_decode(file_get_contents("php://input"), true);

    //check if wallet ids of the receiver and sender are provided
    if (!isset($data['receiverWalletID']) || !isset($data['senderWalletID']) || !isset($data['amount']) || !isset($data['receiverID'])) {
        echo json_encode(['status' => 'error', 'message' => 'Receiver, sender and amount are required']);
        exit();
    }
    $receiverWalletID = $data['receiverWalletID'];
    $senderWalletID = $data['senderWalletID'];
    $amount = $data['amount'];
    $receiverID = $data['receiverID'];

    // get sender's wallet 
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id=:userID AND id=:senderWalletID");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':senderWalletID', $senderWalletID);
    $stmt->execute();
    $senderWallet = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$senderWallet) {
        echo json_encode(['status' => 'error', 'message' => 'Sender wallet not found']);
        exit();
    }
    // get receiver's wallet
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id=:userID AND id=:receiverWalletID");
    $stmt->bindParam(':userID', $receiverID);
    $stmt->bindParam(':receiverWalletID', $receiverWalletID);
    $stmt->execute();
    $receiverWallet = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$receiverWallet) {
        echo json_encode(['status' => 'error', 'message' => 'Receiver wallet not found']);
        exit();
    }
    //check if both wallet has same currency
    if ($senderWallet['currency'] !== $receiverWallet['currency']) {
        echo json_encode(['status' => 'error', 'message' => 'Currencies do not match']);
        exit();
    }
    // check if sender has enough balance
    if ($senderWallet['balance'] < $amount) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient balance']);
        exit();
    }
    // update sender's wallet
    $stmt = $pdo->prepare("UPDATE wallets SET balance=balance-:amount WHERE id=:senderWalletID");
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':senderWalletID', $senderWalletID);
    $stmt->execute();
    // update receiver's wallet
    $stmt = $pdo->prepare("UPDATE wallets SET balance=balance+:amount WHERE id=:receiverWalletID");
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':receiverWalletID', $receiverWalletID);
    $stmt->execute();

    // Create a transaction record for sender 
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id,wallet_id, type, amount, status) VALUES (:userID,:walletID, 'transfer', :amount, 'completed')");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':walletID', $senderWalletID);
    $stmt->execute();
    // Create a transaction record for receiver
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, wallet_id, type, amount, status) VALUES (:userID,:walletID, 'transfer', :amount, 'completed')");
    $stmt->bindParam(':userID', $receiverID);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':walletID', $receiverWalletID);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Transfer successful']);
}