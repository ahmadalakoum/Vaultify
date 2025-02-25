<?php

$server = "localhost";
$username = "root";
$password = "";
$db = "vaultify";

try {
    $pdo = new PDO("mysql:host=$server;dbname=$db", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "connection established";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
