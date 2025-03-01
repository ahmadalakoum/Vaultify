<?php
// require cors
require '../cors.php';

// Require DB connection
require_once "../db_connection/connection.php";
session_start();

// Get raw POST data (JSON)
$data = json_decode(file_get_contents("php://input"), true);

// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['userID'];
$currentPassword = trim($data['currentPassword']);
$newPassword = trim($data['newPassword']);
$confirmPassword = trim($data['confirmPassword']);

// Check for empty fields
if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit();
}

// Check if new passwords match
if ($newPassword !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'New passwords do not match']);
    exit();
}

// Get the current password from the database
$stmt = $pdo->prepare("SELECT password FROM users WHERE id = :user_id");
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Verify current password
if (!$user || !password_verify($currentPassword, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Current password is incorrect']);
    exit();
}

// Hash the new password
$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

// Update the password
$stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
$stmt->bindParam(':password', $hashedPassword);
$stmt->bindParam(':user_id', $user_id);
$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'Password changed successfully']);
exit();

?>