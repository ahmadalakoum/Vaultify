<?php
// Require CORS
require '../cors.php';
// Start session
session_start();

// Require DB connection
require_once "../db_connection/connection.php";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    // Check if user is logged in
    require "../bearer.php";

    // Get the user ID from the session
    $userID = getBearerToken();

    // Get senderWalletID from query parameters
    if (!isset($_GET['senderWalletID'])) {
        echo json_encode(['status' => 'error', 'message' => 'Sender wallet ID is required in the URL']);
        exit();
    }
    $senderWalletID = $_GET['senderWalletID'];

    // Get POST data (Deposit Amount, Receiver Username, and Wallet Name)
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['amount']) || !isset($data['receiverUsername']) || !isset($data['receiverWalletName'])) {
        echo json_encode(['status' => 'error', 'message' => 'Amount, Receiver username, and Receiver wallet name are required']);
        exit();
    }

    $amount = $data['amount'];
    $receiverUsername = $data['receiverUsername'];
    $receiverWalletName = $data['receiverWalletName'];

    // Get sender's wallet
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id=:userID AND id=:senderWalletID");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':senderWalletID', $senderWalletID);
    $stmt->execute();
    $senderWallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$senderWallet) {
        echo json_encode(['status' => 'error', 'message' => 'Sender wallet not found']);
        exit();
    }

    // Get receiver's ID using username
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username=:username");
    $stmt->bindParam(':username', $receiverUsername);
    $stmt->execute();
    $receiver = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receiver) {
        echo json_encode(['status' => 'error', 'message' => 'Receiver username not found']);
        exit();
    }

    $receiverID = $receiver['id'];

    // Get receiver's wallet using wallet name
    $stmt = $pdo->prepare("SELECT id, currency FROM wallets WHERE user_id=:receiverID AND wallet_name=:walletName");
    $stmt->bindParam(':receiverID', $receiverID);
    $stmt->bindParam(':walletName', $receiverWalletName);
    $stmt->execute();
    $receiverWallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receiverWallet) {
        echo json_encode(['status' => 'error', 'message' => 'Receiver wallet not found']);
        exit();
    }

    $receiverWalletID = $receiverWallet['id'];

    // Check if both wallets have the same currency
    if ($senderWallet['currency'] !== $receiverWallet['currency']) {
        echo json_encode(['status' => 'error', 'message' => 'Currencies do not match']);
        exit();
    }

    // Check if sender has enough balance
    if ($senderWallet['balance'] < $amount) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient balance']);
        exit();
    }

    // Update sender's wallet balance
    $stmt = $pdo->prepare("UPDATE wallets SET balance=balance-:amount WHERE id=:senderWalletID");
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':senderWalletID', $senderWalletID);
    $stmt->execute();

    // Update receiver's wallet balance
    $stmt = $pdo->prepare("UPDATE wallets SET balance=balance+:amount WHERE id=:receiverWalletID");
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':receiverWalletID', $receiverWalletID);
    $stmt->execute();

    // Create a transaction record for sender
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, wallet_id, type, amount, status) VALUES (:userID, :walletID, 'transfer', :amount, 'completed')");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':walletID', $senderWalletID);
    $stmt->execute();

    // Create a transaction record for receiver
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, wallet_id, type, amount, status) VALUES (:userID, :walletID, 'transfer', :amount, 'completed')");
    $stmt->bindParam(':userID', $receiverID);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':walletID', $receiverWalletID);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Transfer successful']);
}
