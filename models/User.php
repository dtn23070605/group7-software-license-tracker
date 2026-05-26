<?php
require_once __DIR__ . '/../config/Database.php';

class User {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $username, string $email, string $role, string $departmentId = ''): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, email, role, department_id) VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$username, $email, $role, $departmentId]);
    }

    public function update(int $id, string $username, string $email, string $role, string $departmentId = ''): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE users SET username = ?, email = ?, role = ?, department_id = ? WHERE id = ?"
        );
        return $stmt->execute([$username, $email, $role, $departmentId, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function emailExists(string $email, ?int $excludeId = null): bool {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        return $stmt->fetchColumn() > 0;
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool {
        if ($excludeId) {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt->execute([$username]);
        }
        return $stmt->fetchColumn() > 0;
    }

    public function hasLinkedAllocations(int $id): bool {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM license_allocations WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}
