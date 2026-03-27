<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();
print_r($db->query("SELECT * FROM schools")->fetchAll(PDO::FETCH_ASSOC));
