<?php

require_once __DIR__ . '/../config/database.php';

class TeacherModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT t.*, u.name, u.email,
                       GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR ', ') AS subjects
                FROM teachers t
                JOIN users u ON t.user_id = u.id
                LEFT JOIN subjects s ON s.teacher_id = t.id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (u.name LIKE ? OR t.employee_id LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
        }

        $sql .= " GROUP BY t.id ORDER BY u.name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT t.*, u.name, u.email,
                    GROUP_CONCAT(s.name ORDER BY s.name SEPARATOR ', ') AS subjects
             FROM teachers t
             JOIN users u ON t.user_id = u.id
             LEFT JOIN subjects s ON s.teacher_id = t.id
             WHERE t.id = ?
             GROUP BY t.id LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function employeeIdExists(string $empId): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM teachers WHERE employee_id = ?");
        $stmt->execute([$empId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO teachers (user_id, employee_id, phone, address, joined_date)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['user_id'],
            $data['employee_id'],
            $data['phone']       ?? null,
            $data['address']     ?? null,
            $data['joined_date'] ?? date('Y-m-d'),
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE teachers SET phone=?, address=?, joined_date=? WHERE id=?"
        );
        $stmt->execute([
            $data['phone']       ?? null,
            $data['address']     ?? null,
            $data['joined_date'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("SELECT user_id FROM teachers WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row) {
            $this->db->prepare("DELETE FROM users WHERE id = ?")->execute([$row['user_id']]);
        }
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    }
}
