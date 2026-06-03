<?php
require_once __DIR__ . '/../config/Database.php';

class ActivationLog {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT al.*, u.username, st.name AS software_name
             FROM activation_logs al
             JOIN license_allocations la ON al.allocation_id = la.id
             JOIN users u ON la.user_id = u.id
             JOIN software_titles st ON la.software_id = st.id
             ORDER BY al.activated_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getByAllocation(int $allocationId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM activation_logs WHERE allocation_id = ? ORDER BY activated_at DESC"
        );
        $stmt->execute([$allocationId]);
        return $stmt->fetchAll();
    }

    public function log(int $allocationId): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO activation_logs (allocation_id) VALUES (?)"
        );
        return $stmt->execute([$allocationId]);
    }
}
