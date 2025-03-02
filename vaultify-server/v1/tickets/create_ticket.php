<?php
// require cors
require '../cors.php';
session_start();
require_once "../db_connection/connection.php";

// Check if user is logged in
require "../bearer.php";
//get the user id from the session
$userID = getBearerToken();

// Get POST data (Ticket Subject, Description)
$data = json_decode(file_get_contents("php://input"), true);
if (empty($data['subject']) || empty($data['description'])) {
    echo json_encode(['status' => 'error', 'message' => 'Subject and description are required']);
    exit();
}

$subject = $data['subject'];
$description = $data['description'];

// Insert ticket into database
$stmt = $pdo->prepare("INSERT INTO support_tickets (user_id, subject, description) 
                       VALUES (:userID, :subject, :description)");
$stmt->bindParam(':userID', $userID);
$stmt->bindParam(':subject', $subject);
$stmt->bindParam(':description', $description);
$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'Support ticket created successfully']);
exit();
?>