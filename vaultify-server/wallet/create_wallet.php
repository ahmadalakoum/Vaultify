<?php
session_start();
require_once "../db_connection/connection.php";
// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$userID = $_SESSION['userID'];

// check if the user has a wallet

$stmt = $pdo->prepare("SELECT * FROM wallets WHERE user_id = :userID");
$stmt->execute(['userID' => $userID]);
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$wallet) {
    //create a new wallet for the user
    $stmt = $pdo->prepare("INSERT INTO wallets (user_id,balance) VALUES (:userID,0.00)");
    $stmt->bindParam(':userID', $userID);
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Wallet created successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to create wallet']);
    }
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'user already have a wallet']);
}
