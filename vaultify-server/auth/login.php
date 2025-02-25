<?php


// Require DB connection
require_once "../db_connection/connection.php";

//get raw POST data(JSON)
$data = json_decode(file_get_contents("php://input"), true);

//check if JSON was received
if (empty($data)) {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
    exit();
}

// Extract data from JSON
$email = trim($data['email']);
$password = trim($data['password']);

// check for empty fields
if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter all required fields']);
    exit();
}

//check if the user exists in the database
$stmt = $pdo->prepare("SELECT * FROM users WHERE email=:email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (empty($user)) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit();
}

// Check if the password matches
if (!password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Incorrect credentials']);
    exit();
}

session_start();

$_SESSION['userID'] = $user['id'];
echo json_encode([
    'status' => 'success',
    'message' => 'Login successful',
    'user' => [
        'id' => $user['id'],
        'email' => $user['email'],
        'phone' => $user['phone'],
        'role' => $user['role']
    ]
]);

exit();