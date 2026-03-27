<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();
$db->exec("UPDATE users SET role = 'admin'");
echo "All users promoted to Admin. You now have FULL power over the system.\n";
