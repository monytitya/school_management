<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();
$tables = ['students', 'classes', 'subjects', 'teachers', 'users', 'student_registry'];
foreach ($tables as $table) {
    echo "--- $table ---\n";
    try {
        $stmt = $db->query("DESCRIBE $table");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "{$row['Field']} - {$row['Type']}\n";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
