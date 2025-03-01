<?php
// require cors
require '../cors.php';
session_start();
// Require DB connection
require_once "../db_connection/connection.php";
// Check if user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}
//get the user id from the session
$userID = $_SESSION['userID'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stmt = $pdo->prepare("SELECT email,phone,password,address FROM users WHERE id=:userID");
    $stmt->bindParam(':userID', $userID);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($user) {
        //get raw POST data(JSON)
        $data = json_decode(file_get_contents("php://input"), true);

        //check if JSON was received
        if (empty($data)) {
            echo json_encode(['status' => 'error', 'message' => 'No data received']);
            exit();
        }
        //update phone or address
        $phone = isset($data['phone']) ? trim($data['phone']) : null;
        $address = isset($data['address']) ? trim($data['address']) : null;
        // check for empty fields
        if (empty($phone) && empty($address)) {
            echo json_encode(['status' => 'error', 'message' => 'At least one field is required']);
            exit();
        }
        $query = "UPDATE users SET ";
        $params = [];
        if ($phone !== null) {
            $query .= 'phone =:phone, ';
            $params[':phone'] = $phone;
        }
        if ($address !== null) {
            $query .= 'address =:address ';
            $params[':address'] = $address;

        }
        $params[':userID'] = $userID;
        //remove the last comma
        $query = rtrim($query, ', ') . " WHERE id=:userID";
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        //update the session
        $_SESSION['phone'] = $phone;
        $_SESSION['address'] = $address;
        echo json_encode(['status' => 'success', 'message' => 'User details updated successfully']);
        exit();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
    exit();
}
