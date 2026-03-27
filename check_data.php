<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();
echo "Subjects:\n";
print_r($db->query("SELECT * FROM subjects")->fetchAll(PDO::FETCH_ASSOC));
echo "\nClassrooms:\n";
print_r($db->query("SELECT * FROM classrooms")->fetchAll(PDO::FETCH_ASSOC));
echo "\nClasses:\n";
print_r($db->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC));
