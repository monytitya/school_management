<?php
require_once __DIR__ . '/../config/database.php';

class SchoolModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(): array {
        return $this->db->query("SELECT school_id as id, school_title, level_count, is_active FROM schools ORDER BY school_id DESC")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT school_id as id, school_title, level_count, is_active FROM schools WHERE school_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            "INSERT INTO schools (school_title, level_count, is_active)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([
            $data['school_title'],
            $data['level_count'] ?? 0,
            $data['is_active'] ?? 1,
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare(
            "UPDATE schools SET school_title = ?, level_count = ?, is_active = ? WHERE school_id = ?"
        );
        $stmt->execute([
            $data['school_title'],
            $data['level_count'] ?? 0,
            $data['is_active'] ?? 1,
            $id,
        ]);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM schools WHERE school_id = ?")->execute([$id]);
    }
}
