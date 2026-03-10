<?php

require_once __DIR__ . '/../config/database.php';

class AttendanceModel
{

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getByClass(int $classId, string $date): array
    {
        $stmt = $this->db->prepare(
            "SELECT a.*, u.name AS student_name, s.student_id AS student_code
             FROM attendance a
             JOIN students s ON a.student_id = s.id
             JOIN users u ON s.user_id = u.id
             WHERE a.class_id = ? AND a.date = ?
             ORDER BY u.name ASC"
        );
        $stmt->execute([$classId, $date]);
        return $stmt->fetchAll();
    }

    public function getByStudent(int $studentId, array $filters = []): array
    {
        $sql = "SELECT a.*, c.name AS class_name
                FROM attendance a
                JOIN classes c ON a.class_id = c.id
                WHERE a.student_id = ?";
        $params = [$studentId];

        if (!empty($filters['month'])) {
            $sql .= " AND DATE_FORMAT(a.date, '%Y-%m') = ?";
            $params[] = $filters['month'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY a.date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getSummary(int $studentId): array
    {
        $stmt = $this->db->prepare(
            "SELECT
                COUNT(*) AS total,
                SUM(status = 'present') AS present,
                SUM(status = 'absent')  AS absent,
                SUM(status = 'late')    AS late,
                SUM(status = 'excused') AS excused,
                ROUND(SUM(status = 'present') / COUNT(*) * 100, 1) AS attendance_rate
             FROM attendance WHERE student_id = ?"
        );
        $stmt->execute([$studentId]);
        return $stmt->fetch();
    }

    public function record(array $data): int
    {
        // Upsert: update if already recorded for that day
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

    public function bulkRecord(array $records, int $classId, string $date, int $createdBy): void
    {
        $stmt = $this->db->prepare(
            "INSERT INTO attendance (student_id, class_id, date, status, note, created_by)
             VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE status=VALUES(status), note=VALUES(note)"
        );
        foreach ($records as $r) {
            $stmt->execute([
                $r['student_id'],
                $classId,
                $date,
                $r['status']   ?? 'present',
                $r['note']     ?? null,
                $createdBy,
            ]);
        }
    }

    public function delete(int $id): void
    {
        $this->db->prepare("DELETE FROM attendance WHERE id = ?")->execute([$id]);
    }
}
