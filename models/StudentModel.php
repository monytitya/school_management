<?php
require_once __DIR__ . '/../config/database.php';

class StudentModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT s.*, s.student_full_name as name, s.student_code as student_code, 
                       s.email, c.class_name as class_name
                FROM students s
                LEFT JOIN class_students cs ON s.student_id = cs.student_id
                LEFT JOIN classes c ON cs.class_id = c.class_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['class_id'])) { $sql .= " AND cs.class_id = ?"; $params[] = $filters['class_id']; }
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
                    s.email, c.class_name as class_name, cs.class_id as class_id
             FROM students s
             LEFT JOIN class_students cs ON s.student_id = cs.student_id
             LEFT JOIN classes c ON cs.class_id = c.class_id
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
            "INSERT INTO students (student_code, student_full_name, gender, dob, email, phone)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $data['student_code'] ?? $data['student_id'],
            $data['student_full_name'] ?? $data['name'],
            $data['gender']        ?? null,
            $data['dob']           ?? null,
            $data['email']         ?? null,
            $data['phone']         ?? null,
        ]);
        $studentId = (int)$this->db->lastInsertId();
        
        if (!empty($data['class_id'])) {
            $cStmt = $this->db->prepare("INSERT INTO class_students (class_id, student_id) VALUES (?, ?)");
            $cStmt->execute([$data['class_id'], $studentId]);
        }
        return $studentId;
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE students SET student_code=?, student_full_name=?, gender=?, dob=?, phone=? WHERE student_id=?"
        );
        $stmt->execute([
            $data['student_code'] ?? $data['student_id'],
            $data['student_full_name'] ?? $data['name'],
            $data['gender']        ?? null,
            $data['dob']           ?? null,
            $data['phone']         ?? null,
            $id,
        ]);
        if (isset($data['class_id'])) {
            $del = $this->db->prepare("DELETE FROM class_students WHERE student_id = ?");
            $del->execute([$id]);
            if (!empty($data['class_id'])) {
                $ins = $this->db->prepare("INSERT INTO class_students (class_id, student_id) VALUES (?, ?)");
                $ins->execute([$data['class_id'], $id]);
            }
        }
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("SELECT email FROM students WHERE student_id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && !empty($row['email'])) { $this->db->prepare("DELETE FROM users WHERE email = ?")->execute([$row['email']]); }
        $this->db->prepare("DELETE FROM class_students WHERE student_id = ?")->execute([$id]);
        $this->db->prepare("DELETE FROM students WHERE student_id = ?")->execute([$id]);
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM students")->fetchColumn();
    }
}