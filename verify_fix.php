<?php
require_once __DIR__ . '/models/ClassModel.php';
$model = new ClassModel();
$classes = $model->getAll();
echo "Classes List with Linked Subjects:\n";
foreach ($classes as $c) {
    echo "- Class: {$c['class_name']}, Extracted Stage: {$c['grade_level']}, Extracted Section: {$c['section_name']}, Subject: {$c['subject_title']}\n";
}

require_once __DIR__ . '/models/AttendanceModel.php';
$attModel = new AttendanceModel();
try {
    $attModel->getByClass(10, date('Y-m-d'));
    echo "\nAttendance model verification: PASSED (No crash)\n";
} catch (Exception $e) {
    echo "\nAttendance model verification: FAILED - " . $e->getMessage() . "\n";
}
