<?php
require_once __DIR__ . '/../config/Database.php';

class SoftwareTitle {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query("SELECT * FROM software_titles ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare("SELECT * FROM software_titles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(string $name, string $vendor): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO software_titles (name, vendor) VALUES (?, ?)"
        );
        return $stmt->execute([$name, $vendor]);
    }

    public function update(int $id, string $name, string $vendor): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE software_titles SET name = ?, vendor = ? WHERE id = ?"
        );
        return $stmt->execute([$name, $vendor, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM software_titles WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function nameExists(string $name, ?int $excludeId = null): bool {
        if ($excludeId) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM software_titles WHERE name = ? AND id != ?"
            );
            $stmt->execute([$name, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM software_titles WHERE name = ?"
            );
            $stmt->execute([$name]);
        }
        return $stmt->fetchColumn() > 0;
    }

    public function hasLinkedPools(int $id): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM license_pools WHERE software_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}
