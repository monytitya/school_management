<?php

require_once __DIR__ . '/../config/database.php';

class StudentRegistryModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array
    {
        $sql = 'SELECT * FROM student_registry WHERE 1=1';
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= ' AND (student_full_name LIKE ? OR student_code LIKE ? OR email LIKE ?)';
            $q = '%' . $filters['search'] . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }
        if (isset($filters['school_id']) && $filters['school_id'] !== '' && $filters['school_id'] !== null) {
            $sql .= ' AND school_id = ?';
            $params[] = (int) $filters['school_id'];
        }

        $sql .= ' ORDER BY student_full_name ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $studentId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM student_registry WHERE student_id = ? LIMIT 1');
        $stmt->execute([$studentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function codeExists(string $code, ?int $exceptStudentId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM student_registry WHERE student_code = ?';
        $params = [$code];
        if ($exceptStudentId !== null) {
            $sql .= ' AND student_id != ?';
            $params[] = $exceptStudentId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO student_registry (student_code, student_full_name, gender, dob, email, phone, school_id, stage_id, section_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['student_code'],
            $data['student_full_name'],
            $data['gender'] ?? null,
            $data['dob'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            isset($data['school_id']) ? (int) $data['school_id'] : null,
            isset($data['stage_id']) ? (int) $data['stage_id'] : null,
            isset($data['section_id']) ? (int) $data['section_id'] : null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $studentId, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE student_registry SET
                student_code = ?, student_full_name = ?, gender = ?, dob = ?, email = ?, phone = ?,
                school_id = ?, stage_id = ?, section_id = ?
             WHERE student_id = ?'
        );
        $stmt->execute([
            $data['student_code'],
            $data['student_full_name'],
            $data['gender'] ?? null,
            $data['dob'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            isset($data['school_id']) ? (int) $data['school_id'] : null,
            isset($data['stage_id']) ? (int) $data['stage_id'] : null,
            isset($data['section_id']) ? (int) $data['section_id'] : null,
            $studentId,
        ]);
    }

    public function delete(int $studentId): void
    {
        $stmt = $this->db->prepare('DELETE FROM student_registry WHERE student_id = ?');
        $stmt->execute([$studentId]);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM student_registry')->fetchColumn();
    }
}
