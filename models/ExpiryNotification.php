<?php
require_once __DIR__ . '/../config/Database.php';

class ExpiryNotification {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT en.*, u.username, st.name AS software_name, la.valid_until
             FROM expiry_notifications en
             JOIN license_allocations la ON en.allocation_id = la.id
             JOIN users u ON la.user_id = u.id
             JOIN software_titles st ON la.software_id = st.id
             ORDER BY en.sent_at DESC"
        );
        return $stmt->fetchAll();
    }

    public function getByAllocation(int $allocationId): array {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM expiry_notifications WHERE allocation_id = ? ORDER BY sent_at DESC"
        );
        $stmt->execute([$allocationId]);
        return $stmt->fetchAll();
    }

    public function create(int $allocationId, string $notificationType): bool {
        $stmt = $this->pdo->prepare(
            "INSERT INTO expiry_notifications (allocation_id, notification_type) VALUES (?, ?)"
        );
        return $stmt->execute([$allocationId, $notificationType]);
    }

    public function alreadySent(int $allocationId, string $notificationType): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM expiry_notifications
             WHERE allocation_id = ? AND notification_type = ?"
        );
        $stmt->execute([$allocationId, $notificationType]);
        return $stmt->fetchColumn() > 0;
    }
}
