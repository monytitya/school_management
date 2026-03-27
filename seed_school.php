<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();

$stmt = $db->prepare("INSERT IGNORE INTO schools (school_title, level_count, is_active) VALUES (?, ?, ?)");
$stmt->execute(['Monyadmin School', 12, 1]);
echo "School seeded successfully.\n";
