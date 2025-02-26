<?php
session_start();
require_once "../db_connection/connection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if user is logged in
    if (!isset($_SESSION['userID'])) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit();
    }

    $userID = $_SESSION['userID'];

    // Check if a file was uploaded
    if (!isset($_FILES['id_document'])) {
        echo json_encode(['status' => 'error', 'message' => 'No file uploaded']);
        exit();
    }

    $file = $_FILES['id_document'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];

    // Allowed file types (JPEG, PNG, PDF)
    $allowed = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];

    //check if the file is in the allowed file list
    if (!in_array($fileType, $allowed)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only JPG, PNG,JPEG and PDF allowed']);
        exit();
    }

    // Check for errors in the file upload
    if ($fileError !== 0) {
        echo json_encode(['status' => 'error', 'message' => 'Error uploading file']);
        exit();
    }

    // Limit file size (e.g., max 5MB)
    if ($fileSize > 5 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'File too large. Max size is 5MB']);
        exit();
    }

    // Generate a unique file name
    $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
    $newFileName = "id_" . $userID . "_" . time() . "." . $fileExt;

    // Set upload directory
    $uploadDir = "../uploads/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileDestination = $uploadDir . $newFileName;

    // Move the file to the upload directory
    if (!move_uploaded_file($fileTmpName, $fileDestination)) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to save file']);
        exit();
    }

    // Store file path in the database
    $stmt = $pdo->prepare("INSERT INTO documents (user_id, document_type, file_path) VALUES (:userID, 'ID', :filePath)");
    $stmt->bindParam(':userID', $userID);
    $stmt->bindParam(':filePath', $fileDestination);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'ID uploaded successfully, awaiting verification']);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}
?>