<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();

try {
    // Seed Stages
    $db->exec("INSERT IGNORE INTO stages (stage_id, stage_name) VALUES 
        (1, 'Grade 1'), (2, 'Grade 2'), (3, 'Grade 3'), 
        (4, 'Grade 4'), (5, 'Grade 5'), (6, 'Grade 6'),
        (7, 'Grade 7'), (8, 'Grade 8'), (9, 'Grade 9'),
        (10, 'Grade 10'), (11, 'Grade 11'), (12, 'Grade 12')");
    echo "Stages seeded.\n";

    // Seed Sections
    $db->exec("INSERT IGNORE INTO sections (section_id, section_name) VALUES 
        (1, 'A'), (2, 'B'), (3, 'C'), (4, 'D')");
    echo "Sections seeded.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
