<?php
require_once __DIR__ . '/../config/Database.php';

class AllocationRule {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT ar.*, st.name AS software_name
             FROM allocation_rules ar
             JOIN software_titles st ON ar.software_id = st.id
             ORDER BY ar.id DESC"
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT ar.*, st.name AS software_name
             FROM allocation_rules ar
             JOIN software_titles st ON ar.software_id = st.id
             WHERE ar.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(int $softwareId, string $targetRole, int $durationDays): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO allocation_rules (software_id, target_role, duration_days) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$softwareId, $targetRole, $durationDays]);
    }

    public function update(int $id, int $softwareId, string $targetRole, int $durationDays): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE allocation_rules SET software_id = ?, target_role = ?, duration_days = ? WHERE id = ?"
        );
        return $stmt->execute([$softwareId, $targetRole, $durationDays, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM allocation_rules WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function ruleExists(int $softwareId, string $targetRole, ?int $excludeId = null): bool {
        if ($excludeId) {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM allocation_rules WHERE software_id = ? AND target_role = ? AND id != ?"
            );
            $stmt->execute([$softwareId, $targetRole, $excludeId]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT COUNT(*) FROM allocation_rules WHERE software_id = ? AND target_role = ?"
            );
            $stmt->execute([$softwareId, $targetRole]);
        }
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Lấy rule theo role và software_id.
     * Dùng bởi LicenseAllocationController để lấy duration_days cho strategy.
     */
    public function getByRoleAndSoftware(string $role, int $softwareId): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM allocation_rules
             WHERE target_role = ? AND software_id = ?
             LIMIT 1"
        );
        $stmt->execute([$role, $softwareId]);
        return $stmt->fetch();
    }
}
