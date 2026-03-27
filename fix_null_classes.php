<?php
require_once __DIR__ . '/config/database.php';
$db = Database::connect();

// Find a valid stage and section to use as default (if 10/1 don't exist)
$stage = $db->query("SELECT stage_id FROM stages LIMIT 1")->fetchColumn();
$section = $db->query("SELECT section_id FROM sections LIMIT 1")->fetchColumn();

if (!$stage || !$section) {
    die("Error: No stages or sections found to recover data.");
}

$stmt = $db->prepare("UPDATE classes SET stage_id = ?, section_id = ? WHERE stage_id IS NULL OR section_id IS NULL");
$stmt->execute([$stage, $section]);
$affected = $stmt->rowCount();

echo "Cleanup complete. Fixed $affected records with missing Stage/Section data. They are now linked to Stage ID $stage and Section ID $section.\n";
