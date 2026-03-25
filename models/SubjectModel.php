<?php
require_once __DIR__ . '/../config/database.php';

class SubjectModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT sub.*, sub.subject_id as id, sub.title as name, 
                       '' as subject_code
                FROM subjects sub
                WHERE 1=1";
        $params = [];
        $sql .= " ORDER BY sub.title ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare(
            "SELECT sub.*, sub.subject_id as id, sub.title as name
             FROM subjects sub WHERE subject_id = ? LIMIT 1"
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function codeExists(string $code, int $excludeId = 0): bool {
        // The current database schema does not have a 'code' column.
        return false;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO subjects (title) VALUES (?)"
        );
        $stmt->execute([
            $data['title'] ?? $data['name']
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE subjects SET title=? WHERE subject_id=?"
        );
        $stmt->execute([
            $data['title'] ?? $data['name'],
            $id,
        ]);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM subjects WHERE subject_id = ?")->execute([$id]);
    }
}
