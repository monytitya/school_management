<?php

require_once __DIR__ . '/../config/database.php';

class StudentRegistryModel
{
    private PDO $db;
    private string $table = 'student_registry';
    private bool $requiresManualId = false;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->table = $this->resolveTable();
        $this->requiresManualId = $this->studentIdNeedsManualValue();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE 1=1";
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
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE student_id = ? LIMIT 1");
        $stmt->execute([$studentId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function codeExists(string $code, ?int $exceptStudentId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE student_code = ?";
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
        if ($this->requiresManualId) {
            $nextId = $this->nextStudentId();
            $stmt = $this->db->prepare(
                "INSERT INTO {$this->table} (student_id, student_code, student_full_name, gender, dob, email, phone, school_id, stage_id, section_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->execute([
                $nextId,
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
            return $nextId;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO {$this->table} (student_code, student_full_name, gender, dob, email, phone, school_id, stage_id, section_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
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
            "UPDATE {$this->table} SET
                student_code = ?, student_full_name = ?, gender = ?, dob = ?, email = ?, phone = ?,
                school_id = ?, stage_id = ?, section_id = ?
             WHERE student_id = ?"
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
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE student_id = ?");
        $stmt->execute([$studentId]);
    }

    public function count(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
    }

    private function resolveTable(): string
    {
        // Use whichever table exists with the expected flat student columns.
        if ($this->tableHasColumns('student_registry')) {
            return 'student_registry';
        }
        if ($this->tableHasColumns('students')) {
            return 'students';
        }
        return 'student_registry';
    }

    private function tableHasColumns(string $table): bool
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT COLUMN_NAME FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?'
            );
            $stmt->execute([DB_NAME, $table]);
            $cols = array_map('strtolower', array_column($stmt->fetchAll(), 'COLUMN_NAME'));
            $required = [
                'student_id',
                'student_code',
                'student_full_name',
                'gender',
                'dob',
                'email',
                'phone',
                'school_id',
                'stage_id',
                'section_id',
            ];
            foreach ($required as $col) {
                if (!in_array($col, $cols, true)) {
                    return false;
                }
            }
            return true;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function studentIdNeedsManualValue(): bool
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT is_nullable, extra
                 FROM information_schema.columns
                 WHERE table_schema = ? AND table_name = ? AND column_name = ? LIMIT 1'
            );
            $stmt->execute([DB_NAME, $this->table, 'student_id']);
            $row = $stmt->fetch();
            if (!$row) {
                return false;
            }
            $isNullable = strtoupper((string) ($row['is_nullable'] ?? 'YES')) === 'YES';
            $extra = strtolower((string) ($row['extra'] ?? ''));
            $isAutoIncrement = strpos($extra, 'auto_increment') !== false;
            return !$isNullable && !$isAutoIncrement;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function nextStudentId(): int
    {
        $stmt = $this->db->query("SELECT COALESCE(MAX(student_id), 0) + 1 AS next_id FROM {$this->table}");
        $row = $stmt->fetch();
        return (int) ($row['next_id'] ?? 1);
    }
}
