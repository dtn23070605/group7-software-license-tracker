<?php
require_once __DIR__ . '/../config/Database.php';

class RevocationLog {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT rl.*, u.username, st.name AS software_name
             FROM revocation_logs rl
             JOIN license_allocations la ON rl.allocation_id = la.id
             JOIN users u ON la.user_id = u.id
             JOIN software_titles st ON la.software_id = st.id
             ORDER BY rl.revoked_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getByAllocation(int $allocationId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM revocation_logs WHERE allocation_id = ? ORDER BY revoked_at DESC"
        );
        $stmt->execute([$allocationId]);
        return $stmt->fetchAll();
    }

    public function create(int $allocationId, string $reason): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO revocation_logs (allocation_id, reason) VALUES (?, ?)"
        );
        return $stmt->execute([$allocationId, $reason]);
    }
}
