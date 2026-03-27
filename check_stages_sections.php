<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();
echo "Stages:\n";
print_r($db->query("SELECT * FROM stages")->fetchAll(PDO::FETCH_ASSOC));
echo "\nSections:\n";
print_r($db->query("SELECT * FROM sections")->fetchAll(PDO::FETCH_ASSOC));
