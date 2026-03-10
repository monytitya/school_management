<?php

require_once __DIR__ . '/../config/database.php';

class StudentModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT s.*, u.name, u.email, u.role,
                       c.name AS class_name, c.grade_level,
                       CONCAT(pu.name) AS parent_name
                FROM students s
                JOIN users u ON s.user_id = u.id
                LEFT JOIN classes c ON s.class_id = c.id
                LEFT JOIN parents p ON s.parent_id = p.id
                LEFT JOIN users pu ON p.user_id = pu.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['class_id'])) {
            $sql .= " AND s.class_id = ?";
            $params[] = $filters['class_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR s.student_id LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        $sql .= " ORDER BY u.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT s.*, u.name, u.email,
                    c.name AS class_name,
                    CONCAT(pu.name) AS parent_name
             FROM students s
             JOIN users u ON s.user_id = u.id
             LEFT JOIN classes c ON s.class_id = c.id
             LEFT JOIN parents p ON s.parent_id = p.id
             LEFT JOIN users pu ON p.user_id = pu.id
             WHERE s.id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByUserId(int $userId): ?array {
        $stmt = $this->db->prepare("SELECT * FROM students WHERE user_id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function studentIdExists(string $studentId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM students WHERE student_id = ?");
        $stmt->execute([$studentId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO students (user_id, student_id, class_id, parent_id, date_of_birth, gender, phone, address, enrolled_date)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['student_id'],
            $data['class_id']      ?? null,
            $data['parent_id']     ?? null,
            $data['date_of_birth'] ?? null,
            $data['gender']        ?? null,
            $data['phone']         ?? null,
            $data['address']       ?? null,
            $data['enrolled_date'] ?? date('Y-m-d'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE students SET class_id=?, parent_id=?, date_of_birth=?, gender=?, phone=?, address=?
             WHERE id=?"
        );
        $stmt->execute([
            $data['class_id']      ?? null,
            $data['parent_id']     ?? null,
            $data['date_of_birth'] ?? null,
            $data['gender']        ?? null,
            $data['phone']         ?? null,
            $data['address']       ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void {
        // Deleting student also deletes user (CASCADE)
        $stmt = $this->db->prepare("SELECT user_id FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$row['user_id']]);
        }
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    }
}
