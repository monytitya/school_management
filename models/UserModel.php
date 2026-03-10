<?php

require_once __DIR__ . '/../config/database.php';

class UserModel {

    private PDO $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function create(string $name, string $email, string $password, string $role): int {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare(
            "INSERT INTO users (name, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())"
        );
        $stmt->execute([$name, $email, $hash, $role]);
        return (int) $this->db->lastInsertId();
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function updatePassword(int $id, string $newPassword): void {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = $this->db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hash, $id]);
    }

    public function getAll(string $role = null): array {
        if ($role) {
            $stmt = $this->db->prepare("SELECT id, name, email, role, created_at FROM users WHERE role = ? ORDER BY name");
            $stmt->execute([$role]);
        } else {
            $stmt = $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY name");
        }
        return $stmt->fetchAll();
    }
}
