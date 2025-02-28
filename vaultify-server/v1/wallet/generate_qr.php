<?php
// Start session
session_start();

// Require database connection
require_once "../db_connection/connection.php";
require_once "../../../vendor/autoload.php"; // QR code library

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Get user ID from session
$userID = $_SESSION['userID'];

// Fetch user's wallet details from the database
$stmt = $pdo->prepare("SELECT id, wallet_name FROM wallets WHERE user_id = :userID");
$stmt->bindParam(':userID', $userID);
$stmt->execute();
$wallet = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$wallet) {
    echo json_encode(['status' => 'error', 'message' => 'Wallet not found']);
    exit();
}

// Data to encode in the QR code (wallet ID and name)
$walletData = json_encode([
    'wallet_id' => $wallet['id'],
    'wallet_name' => $wallet['wallet_name']
]);

// QR Code options
$options = new QROptions([
    'eccLevel' => QRCode::ECC_L,
    'outputType' => QRCode::OUTPUT_IMAGE_PNG,
    'imageBase64' => false,
    'scale' => 5
]);

// Generate QR Code
$qrCode = new QRCode($options);
$qrImage = $qrCode->render($walletData);

// Output QR Code as an image
header('Content-Type: image/png');
echo $qrImage;
exit();
?>