<?php
// Start session
session_start();

// Require DB connection
require_once "../db_connection/connection.php";

// Check if the user is logged in
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }

    // Get user ID from session 
    $userID = $_SESSION['userID'];
    $data = json_decode(file_get_contents("php://input"), true);
    if (empty($data['wallet_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Wallet ID is required']);
        exit();
    }
    $walletID = $data['wallet_id'];
    // Fetch transactions for the logged-in user
    $stmt = $pdo->prepare("SELECT type, amount, status, timestamp FROM transactions WHERE user_id = :userID and type='deposit' and wallet_id=:WalletID");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':WalletID', $walletID);
    $stmt->execute();
    $deposits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the transactions as JSON
    if ($deposits) {
        echo json_encode(['status' => 'success', 'deposits' => $deposits]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No deposits found']);
    }
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}
