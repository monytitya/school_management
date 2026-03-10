<?php

require_once __DIR__ . '/../config/database.php';

class ClassModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(): array {
        return $this->db->query(
            "SELECT c.*, u.name AS teacher_name,
                    COUNT(DISTINCT s.id) AS student_count,
                    COUNT(DISTINCT sub.id) AS subject_count
             FROM classes c
             LEFT JOIN teachers t ON c.teacher_id = t.id
             LEFT JOIN users u ON t.user_id = u.id
             LEFT JOIN students s ON s.class_id = c.id
             LEFT JOIN subjects sub ON sub.class_id = c.id
             GROUP BY c.id
             ORDER BY c.grade_level, c.name"
        )->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT c.*, u.name AS teacher_name
             FROM classes c
             LEFT JOIN teachers t ON c.teacher_id = t.id
             LEFT JOIN users u ON t.user_id = u.id
             WHERE c.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO classes (name, grade_level, teacher_id, academic_year)
             VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['name'],
            $data['grade_level'],
            $data['teacher_id']    ?? null,
            $data['academic_year'] ?? date('Y') . '-' . (date('Y') + 1),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE classes SET name=?, grade_level=?, teacher_id=?, academic_year=? WHERE id=?"
        );
        $stmt->execute([
            $data['name'],
            $data['grade_level'],
            $data['teacher_id']    ?? null,
            $data['academic_year'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM classes WHERE id = ?")->execute([$id]);
    }

    public function getStudents(int $classId): array {
        $stmt = $this->db->prepare(
            "SELECT s.*, u.name, u.email FROM students s
             JOIN users u ON s.user_id = u.id
             WHERE s.class_id = ? ORDER BY u.name"
        );
        $stmt->execute([$classId]);
        return $stmt->fetchAll();
    }
}
