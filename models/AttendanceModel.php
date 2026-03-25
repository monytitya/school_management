<?php
require_once __DIR__ . '/../config/database.php';

class AttendanceModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getByClass(int $classId, string $date): array {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.name AS student_name, s.student_code AS student_code
             FROM attendance a
             JOIN students s ON a.student_id = s.student_id
             JOIN users u ON s.user_id = u.id
             WHERE a.class_id = ? AND a.date = ?
             ORDER BY u.name ASC"
        );
        $stmt->execute([$classId, $date]);
        return $stmt->fetchAll();
    }

    public function record(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO attendance (student_id, class_id, date, status, note, created_by)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status=VALUES(status), note=VALUES(note)"
        );
        $stmt->execute([
            $data['student_id'],
            $data['class_id'],
            $data['date'],
            $data['status']     ?? 'present',
            $data['note']       ?? null,
            $data['created_by'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function bulkRecord(array $records, int $classId, string $date, int $createdBy): void {
        $stmt = $this->db->prepare(
            "INSERT INTO attendance (student_id, class_id, date, status, note, created_by)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status=VALUES(status), note=VALUES(note)"
        );
        foreach ($records as $r) {
            $stmt->execute([$r['student_id'], $classId, $date, $r['status'] ?? 'present', $r['note'] ?? null, $createdBy]);
        }
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM attendance WHERE id = ?")->execute([$id]);
    }
}
