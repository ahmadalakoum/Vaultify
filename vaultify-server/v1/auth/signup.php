<?php
// require cors
require '../cors.php';
// Require DB connection
require_once "../db_connection/connection.php";

// Get raw POST data (JSON)
$data = json_decode(file_get_contents("php://input"), true);

// Check if JSON was received
if (!$data) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
    exit();
}

// Extract data from JSON
$username = trim($data['username']);
$email = trim($data['email']);
$phone = trim($data['phone']);
$password = trim($data['password']);
$address = trim($data['address']);
$confirmPassword = trim($data['confirmPassword']);

// Check for empty fields
if (empty($username) || empty($email) || empty($phone) || empty($password) || empty($address) || empty($confirmPassword)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter all required fields']);
    exit();
}

// Check if email or phone already exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone");
$stmt->bindParam(':email', $email);
$stmt->bindParam(':phone', $phone);
$stmt->execute();
$user = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!empty($user)) {
    echo json_encode(['status' => 'error', 'message' => 'Email or phone number already exists']);
    exit();
}

// Check if passwords match
if ($password !== $confirmPassword) {
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match']);
    exit();
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

// Insert user into DB
$stmt = $pdo->prepare("INSERT INTO users (username,email, phone, password,address) VALUES (:username,:email, :phone, :password,:address)");
$stmt->bindParam(':username', $username);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':phone', $phone);
$stmt->bindParam(':password', $hashedPassword);
$stmt->bindParam(':address', $address);
$stmt->execute();

echo json_encode(['status' => 'success', 'message' => 'User registered successfully']);
exit();

?>