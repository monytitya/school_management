<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();

$subjectsToAdd = ['English', 'C++', 'HTML CSS JS'];
foreach ($subjectsToAdd as $s) {
    $stmt = $db->prepare("SELECT subject_id FROM subjects WHERE title = ?");
    $stmt->execute([$s]);
    if (!$stmt->fetch()) {
        $db->prepare("INSERT INTO subjects (title) VALUES (?)")->execute([$s]);
        echo "Added subject: $s\n";
    }
}

$classes = $db->query("SELECT class_id, class_name FROM classes")->fetchAll();
foreach ($classes as $c) {
    $stmt = $db->prepare("SELECT subject_id FROM subjects WHERE title = ?");
    $stmt->execute([$c['class_name']]);
    $sub = $stmt->fetch();
    if ($sub) {
        $db->prepare("UPDATE classes SET subject_id = ? WHERE class_id = ?")
            ->execute([$sub['subject_id'], $c['class_id']]);
        echo "Linked class '{$c['class_name']}' to subject ID {$sub['subject_id']}\n";
    }
}

$db->exec("UPDATE classes SET section_id = 1 WHERE class_id = 10 AND section_id IS NULL");
echo "Fixed Row 10 section_id.\n";
