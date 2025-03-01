<?php
// require cors
require '../cors.php';
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
    //check if the user is verified or not 
    $stmt = $pdo->prepare("SELECT verification_status,daily_limit FROM users WHERE id = :userID");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $daily_limit = $user['daily_limit'];
    // Get POST data (Deposit Amount)
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data) || empty($data['amount']) || empty($data['wallet_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Wallet Id And Amount are required']);
        exit();
    }
    $walletID = intval($data['wallet_id']);
    $amount = floatval($data['amount']);
    $stmt = $pdo->prepare("SELECT SUM(amount) as total_today FROM transactions WHERE user_id = :userID AND type = 'deposit' AND DATE(timestamp) = CURDATE()");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $depositToday = $stmt->fetch(PDO::FETCH_ASSOC)['total_today'] ?? 0;

    if (($depositToday + $amount) > $daily_limit) {
        echo json_encode(['status' => 'error', 'message' => 'Daily deposit limit exceeded. You have already deposited ' . $depositToday . ' USD today.']);
        exit();
    }
    // Ensure the amount is positive
    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Deposit amount must be greater than 0']);
        exit();
    }

    // Check if the user has a wallet
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = :userID AND id=:walletID");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':walletID', $walletID);
    $stmt->execute();
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wallet) {
        echo json_encode(['status' => 'error', 'message' => 'Wallet not found for user']);
        exit();
    }

    // Update wallet balance by adding the deposit amount
    $newBalance = $wallet['balance'] + $amount;
    $stmt = $pdo->prepare("UPDATE wallets SET balance = :newBalance WHERE user_id = :userID AND id=:walletID");
    $stmt->bindParam(':newBalance', $newBalance);
    $stmt->bindParam(':walletID', $walletID);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();

    // Create a transaction record 
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id,wallet_id, type, amount, status) VALUES (:userID,:walletID, 'deposit', :amount, 'completed')");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':amount', $amount);
    $stmt->bindParam(':walletID', $walletID);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Deposit successful', 'new_balance' => $newBalance]);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}
?>