<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();

$rooms = ['Room 101', 'Room 102', 'Lab A', 'Main Hall'];
foreach ($rooms as $r) {
    $stmt = $db->prepare("SELECT classroom_id FROM classrooms WHERE room_name = ?");
    $stmt->execute([$r]);
    if (!$stmt->fetch()) {
        $db->prepare("INSERT INTO classrooms (room_name, capacity) VALUES (?, ?)")->execute([$r, 30]);
        echo "Added classroom: $r\n";
    }
}
