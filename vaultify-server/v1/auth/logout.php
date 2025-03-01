<?php
// require cors
require '../cors.php';
//logout the user
session_start();
session_destroy();

echo json_encode([
    'status' => 'success',
    'message' => 'Logout successful'
]);