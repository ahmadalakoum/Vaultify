<?php
session_start();

// require cors
require '../cors.php';
// Require DB connection
require_once "../db_connection/connection.php";
// Check if user is logged in
require "../bearer.php";
//get the user id from the session
$userID = getBearerToken();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    //fetch the user details
    $stmt = $pdo->prepare("SELECT email,phone,address,verification_status,daily_limit FROM users WHERE id=:userID");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
        exit();
    }
    echo json_encode(['status' => 'success', 'message' => 'User details fetched successfully', 'data' => $user]);
    exit();

} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}