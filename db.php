<?php
$host = 'localhost';
$port = 3307;               // change if your MySQL uses a custom port
$db   = 'school_db';
$user = 'root';
$pass = 'mony2024**2000';  // make sure this is correct
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    // Optional: echo "Database connected successfully!";
} catch (\PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    exit;
}
