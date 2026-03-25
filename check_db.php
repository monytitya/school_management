<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();
$output = "";

function desc($table) {
    global $db, $output;
    try {
        $stmt = $db->query("DESCRIBE $table");
        $output .= "\nTable: $table\n";
        while($row = $stmt->fetch()) {
            $output .= " - {$row['Field']} ({$row['Type']})\n";
        }
        $stmt = $db->query("SELECT * FROM $table LIMIT 5");
        $output .= "Sample Data:\n";
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $output .= " " . json_encode($row) . "\n";
        }
    } catch(Exception $e) {
        $output .= "\nTable: $table - Error: " . $e->getMessage() . "\n";
    }
}

foreach(['classes', 'students', 'subjects', 'teachers', 'users', 'stages', 'sections'] as $t) {
    desc($t);
}

file_put_contents('db_dump_v2.txt', $output);
