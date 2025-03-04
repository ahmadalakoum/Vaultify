<?php
require "../cors.php";
// Start session
session_start();

// Require database connection
require_once "../db_connection/connection.php";

require "../bearer.php";
//get the user id from the session
$userID = getBearerToken();
// Check if the user is logged in and is an admin
if (!isset($userID)) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}


// Fetch total users & new users this month
$stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];

$stmt = $pdo->query("SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)");
$newUsers = $stmt->fetch(PDO::FETCH_ASSOC)['new_users'];

// Fetch total transactions
$stmt = $pdo->query("SELECT COUNT(*) as total_transactions FROM transactions");
$totalTransactions = $stmt->fetch(PDO::FETCH_ASSOC)['total_transactions'];

// Fetch transaction amounts grouped by currency
$stmt = $pdo->query("
    SELECT 
        wallets.currency,
        SUM(CASE WHEN transactions.type = 'deposit' THEN transactions.amount ELSE 0 END) as total_deposits,
        SUM(CASE WHEN transactions.type = 'withdraw' THEN transactions.amount ELSE 0 END) as total_withdrawals,
        SUM(CASE WHEN transactions.type = 'transfer' THEN transactions.amount ELSE 0 END) as total_transfers
    FROM transactions
    JOIN wallets ON transactions.wallet_id = wallets.id
    GROUP BY wallets.currency
");

$transactionStats = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $transactionStats[$row['currency']] = [
        'total_deposits' => $row['total_deposits'] ?: 0,
        'total_withdrawals' => $row['total_withdrawals'] ?: 0,
        'total_transfers' => $row['total_transfers'] ?: 0
    ];
}

// Prepare response
$response = [
    'status' => 'success',
    'data' => [
        'user_growth' => [
            'total_users' => $totalUsers,
            'new_users_this_month' => $newUsers
        ],
        'transactions' => [
            'total_transactions' => $totalTransactions,
            'currencies' => $transactionStats
        ]
    ]
];

// Return JSON response
echo json_encode($response);
exit();
?>