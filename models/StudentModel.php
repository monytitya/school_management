<?php
require_once __DIR__ . '/../config/database.php';

class StudentModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT s.*, s.student_full_name as name, s.student_code as student_code, 
                       u.email, c.class_name as class_name
                FROM students s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN classes c ON s.class_id = c.class_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['class_id'])) { $sql .= " AND s.class_id = ?"; $params[] = $filters['class_id']; }
        if (!empty($filters['search'])) {
            $sql .= " AND (s.student_full_name LIKE ? OR s.student_code LIKE ?)";
            $q = "%{$filters['search']}%"; $params[] = $q; $params[] = $q;
        }
        $sql .= " ORDER BY s.student_full_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT s.*, s.student_full_name as name, s.student_code as student_code, 
                    u.email, c.class_name as class_name
             FROM students s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN classes c ON s.class_id = c.class_id
             WHERE s.student_id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function codeExists(string $code, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM students WHERE student_code = ? AND student_id != ?");
        $stmt->execute([$code, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO students (user_id, student_code, student_full_name, gender, dob, email, phone, address, enrolled_date, class_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['student_code'] ?? $data['student_id'],
            $data['student_full_name'] ?? $data['name'],
            $data['gender']        ?? null,
            $data['dob']           ?? null,
            $data['email']         ?? null,
            $data['phone']         ?? null,
            $data['address']       ?? null,
            $data['enrolled_date'] ?? date('Y-m-d'),
            $data['class_id']      ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE students SET student_code=?, student_full_name=?, gender=?, dob=?, phone=?, address=?, class_id=? WHERE student_id=?"
        );
        $stmt->execute([
            $data['student_code'] ?? $data['student_id'],
            $data['student_full_name'] ?? $data['name'],
            $data['gender']        ?? null,
            $data['dob']           ?? null,
            $data['phone']         ?? null,
            $data['address']       ?? null,
            $data['class_id']      ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("SELECT user_id FROM students WHERE student_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) { $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$row['user_id']]); }
        $this->db->prepare("DELETE FROM students WHERE student_id = ?")->execute([$id]);
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    }
}