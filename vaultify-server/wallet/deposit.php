<?php
// Start session
session_start();

// Require DB connection
require_once "../db_connection/connection.php";

// Check if the user is logged in and if the deposit amount is provided
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user ID from session (if user is logged in)
    $userID = $_SESSION['userID'];

    // Get POST data (Deposit Amount)
    $data = json_decode(file_get_contents("php://input"), true);

    if (empty($data) || empty($data['amount'])) {
        echo json_encode(['status' => 'error', 'message' => 'Amount is required']);
        exit();
    }

    $amount = floatval($data['amount']);

    // Ensure the amount is positive
    if ($amount <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Deposit amount must be greater than 0']);
        exit();
    }

    // Check if the user has a wallet
    $stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = :userID");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $wallet = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$wallet) {
        echo json_encode(['status' => 'error', 'message' => 'Wallet not found for user']);
        exit();
    }

    // Update wallet balance by adding the deposit amount
    $newBalance = $wallet['balance'] + $amount;
    $stmt = $pdo->prepare("UPDATE wallets SET balance = :newBalance WHERE user_id = :userID");
    $stmt->bindParam(':newBalance', $newBalance);
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();

    // Create a transaction record (optional, but good practice for tracking)
    $stmt = $pdo->prepare("INSERT INTO transactions (user_id, type, amount, status) VALUES (:userID, 'deposit', :amount, 'completed')");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':amount', $amount);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Deposit successful', 'new_balance' => $newBalance]);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}
?>