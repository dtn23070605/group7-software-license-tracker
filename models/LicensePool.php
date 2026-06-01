<?php
require_once __DIR__ . '/../config/Database.php';

class LicensePool {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT lp.*, st.name AS software_name
             FROM license_pools lp
             JOIN software_titles st ON lp.software_id = st.id
             ORDER BY lp.id DESC"
        );
        return $stmt->fetchAll();
    }

    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT lp.*, st.name AS software_name
             FROM license_pools lp
             JOIN software_titles st ON lp.software_id = st.id
             WHERE lp.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create(int $softwareId, int $totalQty, int $availableQty, string $expiryDate): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO license_pools (software_id, total_quantity, available_quantity, expiry_date)
             VALUES (?, ?, ?, ?)"
        );
        return $stmt->execute([$softwareId, $totalQty, $availableQty, $expiryDate]);
    }

    public function update(int $id, int $softwareId, int $totalQty, int $availableQty, string $expiryDate): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE license_pools
             SET software_id = ?, total_quantity = ?, available_quantity = ?, expiry_date = ?
             WHERE id = ?"
        );
        return $stmt->execute([$softwareId, $totalQty, $availableQty, $expiryDate, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM license_pools WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function hasLinkedAllocations(int $id): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM license_allocations WHERE pool_id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}
