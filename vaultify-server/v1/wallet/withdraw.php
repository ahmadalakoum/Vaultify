<?php
// Start session
session_start();

// Require DB connection
require_once "../db_connection/connection.php";

// Check if the user is logged in and if the deposit amount is provided
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }
    // Get user ID from session (if user is logged in)
    $userID = $_SESSION['userID'];

    // Get POST data (Deposit Amount)
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data) || empty($data['amount']) || empty($data['wallet_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Wallet id and Amount are required']);
        exit();
    }
    $walletID = intval($data['wallet_id']);
    $amount = floatval($data['amount']);
    // Ensure the amount is positive
    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Deposit amount must be greater than 0']);
        exit();
    }
    // Check if the user has a wallet
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = :userID AND id=:walletID");
    $stmt->bindParam(':walletID', $walletID);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wallet) {
        echo json_encode(['status' => 'error', 'message' => 'Wallet not found for user']);
        exit();
    }
    //check if the user's wallet has enough money to withdraw
    if ($wallet['balance'] < $amount) {
        echo json_encode(['status' => 'error', 'message' => 'Insufficient funds in wallet']);
        exit();
    }
    // Update the wallet balance
    $newBalance = $wallet['balance'] - $amount;
    $stmt = $pdo->prepare("UPDATE wallets SET balance = :newBalance WHERE user_id = :userID AND id=:walletID");
    $stmt->bindParam(':newBalance', $newBalance);
    $stmt->bindParam(':walletID', $walletID);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();

    // Create a transaction record 
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id,wallet_id, type, amount, status) VALUES (:userID,:walletID, 'withdrawal', :amount, 'completed')");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':walletID', $walletID);
    $stmt->bindParam(':amount', $amount);
    $stmt->execute();
    echo json_encode(['status' => 'success', 'message' => 'Withdraw successful', 'balance' => $newBalance]);
    exit();

}