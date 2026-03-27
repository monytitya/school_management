<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();

$table = 'student_registry';
$stmt = $db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '".DB_NAME."' AND table_name = 'student_registry'");
if ((int)$stmt->fetchColumn() === 0) {
    $table = 'students';
}

// Update all students to have valid defaults from the seeded tables
$db->exec("UPDATE $table SET school_id = 1, stage_id = 1, section_id = 1 WHERE school_id IS NULL OR school_id = 0");
echo "Updated student registry with default valid IDs.\n";
