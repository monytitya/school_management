<?php
require_once __DIR__ . '/../config/database.php';

class SubjectModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT sub.*, sub.subject_name as name, sub.subject_code as code, 
                       c.class_name as class_name, u.name AS teacher_name
                FROM subjects sub
                LEFT JOIN classes c ON sub.class_id = c.class_id
                LEFT JOIN teachers t ON sub.teacher_id = t.teacher_id
                LEFT JOIN users u ON t.user_id = u.id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['class_id'])) { $sql .= " AND sub.class_id = ?"; $params[] = $filters['class_id']; }
        if (!empty($filters['teacher_id'])) { $sql .= " AND sub.teacher_id = ?"; $params[] = $filters['teacher_id']; }
        $sql .= " ORDER BY sub.subject_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT sub.*, sub.subject_name as name, sub.subject_code as code
             FROM subjects sub WHERE subject_id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function codeExists(string $code, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM subjects WHERE subject_code = ? AND subject_id != ?");
        $stmt->execute([$code, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO subjects (subject_name, subject_code, class_id, teacher_id) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['subject_name'] ?? $data['name'],
            strtoupper($data['subject_code'] ?? $data['code']),
            $data['class_id']   ?? null,
            $data['teacher_id'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE subjects SET subject_name=?, subject_code=?, class_id=?, teacher_id=? WHERE subject_id=?"
        );
        $stmt->execute([
            $data['subject_name'] ?? $data['name'],
            strtoupper($data['subject_code'] ?? $data['code']),
            $data['class_id']   ?? null,
            $data['teacher_id'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM subjects WHERE subject_id = ?")->execute([$id]);
    }
}
