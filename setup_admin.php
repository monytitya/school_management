<?php
require_once __DIR__ . '/config/database.php';

$db = Database::connect();

$name = 'System Admin';
$email = 'admin@school.com';
$password = 'admin123';
$role = 'admin';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

$db->prepare("DELETE FROM users WHERE email = ?")->execute([$email]);
$stmt = $db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->execute([$name, $email, $hash, $role]);

echo "<h2 style='font-family:sans-serif;color:green'>Admin user reset successfully!</h2>";
echo "<p style='font-family:sans-serif'>Email: <b>$email</b><br>Password: <b>$password</b></p>";
echo "<p style='font-family:sans-serif'><a href='login.php'>→ Go to Login</a></p>";
echo "<p style='color:red;font-family:sans-serif'><b>⚠ Delete this file after use!</b></p>";
