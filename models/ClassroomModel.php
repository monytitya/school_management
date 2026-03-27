<?php
require_once __DIR__ . '/../config/database.php';

class ClassroomModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function getAll(): array {
        return $this->db->query("SELECT classroom_id as id, room_name as name, capacity FROM classrooms ORDER BY room_name ASC")->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT classroom_id as id, room_name as name, capacity FROM classrooms WHERE classroom_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO classrooms (room_name, capacity) VALUES (?, ?)");
        $stmt->execute([$data['name'], $data['capacity'] ?? 30]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void {
        $stmt = $this->db->prepare("UPDATE classrooms SET room_name = ?, capacity = ? WHERE classroom_id = ?");
        $stmt->execute([$data['name'], $data['capacity'] ?? 30, $id]);
    }

    public function delete(int $id): void {
        $this->db->prepare("DELETE FROM classrooms WHERE classroom_id = ?")->execute([$id]);
    }
}
