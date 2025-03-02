<?php
// require cors
require '../cors.php';
session_start();
require_once "../db_connection/connection.php";
// Check if user is logged in
require "../bearer.php";
//get the user id from the session
$userID = getBearerToken();

$stmt = $pdo->prepare("SELECT wallet_name , balance,currency FROM wallets WHERE user_id=:userID");
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$wallets = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode(['status' => 'success', 'wallets' => $wallets]);
exit();
?>