<?php
require_once 'config/database.php';
$db = Database::connect();
print_r($db->query("DESCRIBE users")->fetchAll(PDO::FETCH_ASSOC));
print_r($db->query("SELECT * FROM users LIMIT 1")->fetch(PDO::FETCH_ASSOC));
