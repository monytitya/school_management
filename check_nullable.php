<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();
$stmt = $db->query("SHOW COLUMNS FROM classes");
echo "Column | Type | Null | Key | Default\n";
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $c) {
    echo "{$c['Field']} | {$c['Type']} | {$c['Null']} | {$c['Key']} | {$c['Default']}\n";
}
