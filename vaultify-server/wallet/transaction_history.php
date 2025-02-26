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

    // Fetch transactions for the logged-in user
    $stmt = $pdo->prepare("SELECT type, amount, status, timestamp FROM transactions WHERE user_id = :userID");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $transactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the transactions as JSON
    if ($transactions) {
        echo json_encode(['status' => 'success', 'transactions' => $transactions]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No transactions found']);
    }
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}
