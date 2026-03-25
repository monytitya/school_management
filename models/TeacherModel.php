<?php
require_once __DIR__ . '/../config/database.php';

class TeacherModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT t.*, t.teacher_id AS id, t.teacher_full_name AS name, t.teacher_code AS employee_id, t.email as user_email
                FROM teachers t WHERE 1=1";
        $params = [];
        if (!empty($filters['search'])) {
            $sql .= " AND (t.teacher_full_name LIKE ? OR t.teacher_code LIKE ? OR t.email LIKE ?)";
            $q = "%{$filters['search']}%"; $params[] = $q; $params[] = $q; $params[] = $q;
        }
        $sql .= " ORDER BY t.teacher_id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT t.*, t.teacher_id as id, t.teacher_full_name as name, t.teacher_code as employee_id FROM teachers t WHERE teacher_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function codeExists(string $code, int $excludeId = 0): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM teachers WHERE teacher_code = ? AND teacher_id != ?");
        $stmt->execute([$code, $excludeId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    private function normalizeData(array $data): array {
        return [
            'teacher_code' => $data['teacher_code'] ?? null,
            'teacher_full_name' => $data['teacher_full_name'] ?? null,
            'gender' => !empty($data['gender']) ? $data['gender'] : null,
            'dob' => !empty($data['dob']) ? $data['dob'] : null,
            'email' => !empty($data['email']) ? $data['email'] : null,
            'phone' => !empty($data['phone']) ? $data['phone'] : null,
            'profile_image' => !empty($data['profile_image']) ? $data['profile_image'] : ($data['old_profile_image'] ?? null),
            'joined_date' => !empty($data['joined_date']) ? $data['joined_date'] : null,
            'address' => !empty($data['address']) ? $data['address'] : null
        ];
    }

    public function create(array $data): int {
        $d = $this->normalizeData($data);
        $stmt = $this->db->prepare(
            "INSERT INTO teachers (teacher_code, teacher_full_name, gender, dob, email, phone, profile_image, joined_date, address) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            $d['teacher_code'], $d['teacher_full_name'], $d['gender'], $d['dob'],
            $d['email'], $d['phone'], $d['profile_image'], $d['joined_date'], $d['address']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $d = $this->normalizeData($data);
        $stmt = $this->db->prepare(
            "UPDATE teachers SET teacher_code=?, teacher_full_name=?, gender=?, dob=?, email=?, phone=?, profile_image=?, joined_date=?, address=? WHERE teacher_id=?"
        );
        $stmt->execute([
            $d['teacher_code'], $d['teacher_full_name'], $d['gender'], $d['dob'],
            $d['email'], $d['phone'], $d['profile_image'], $d['joined_date'], $d['address'], $id,
        ]);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM teachers WHERE teacher_id = ?")->execute([$id]);
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM teachers")->fetchColumn();
    }
}
