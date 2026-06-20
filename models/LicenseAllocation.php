<?php
require_once __DIR__ . '/../config/Database.php';

class LicenseAllocation {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll(): array {
        $stmt = $this->pdo->query(
            "SELECT la.*, u.username, st.name AS software_name, lp.expiry_date AS pool_expiry
             FROM license_allocations la
             JOIN users u ON la.user_id = u.id
             JOIN software_titles st ON la.software_id = st.id
             JOIN license_pools lp ON la.pool_id = lp.id
             ORDER BY la.id DESC"
        );
        return $stmt->fetchAll();
    }
/**
     * RBAC: lấy allocations của riêng 1 user.
     * Dùng khi Student đăng nhập, chỉ xem được license của chính mình.
     */
    public function getAllByUser(int $userId): array {
        $stmt = $this->pdo->prepare(
            "SELECT la.*, u.username, st.name AS software_name, lp.expiry_date AS pool_expiry
             FROM license_allocations la
             JOIN users u ON la.user_id = u.id
             JOIN software_titles st ON la.software_id = st.id
             JOIN license_pools lp ON la.pool_id = lp.id
             WHERE la.user_id = ?
             ORDER BY la.id DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    public function getById(int $id): array|false {
        $stmt = $this->pdo->prepare(
            "SELECT la.*, u.username, st.name AS software_name
             FROM license_allocations la
             JOIN users u ON la.user_id = u.id
             JOIN software_titles st ON la.software_id = st.id
             WHERE la.id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Tạo allocation mới và trả về ID vừa tạo.
     * Lấy lastInsertId() NGAY SAU INSERT, trước khi chạy UPDATE pool,
     * vì UPDATE sẽ làm mất giá trị lastInsertId() của INSERT trước đó.
     */
    public function create(int $poolId, int $softwareId, int $userId, string $validUntil): int|false {
        $stmt = $this->pdo->prepare(
            "INSERT INTO license_allocations (pool_id, software_id, user_id, valid_until, status)
             VALUES (?, ?, ?, ?, 'ACTIVE')"
        );
        $result = $stmt->execute([$poolId, $softwareId, $userId, $validUntil]);

        if (!$result) {
            return false;
        }

        // Lấy ID ngay lập tức, trước khi chạy query UPDATE tiếp theo
        $newId = (int)$this->pdo->lastInsertId();

        // Decrease available quantity in pool
        $this->pdo->prepare(
            "UPDATE license_pools SET available_quantity = available_quantity - 1 WHERE id = ? AND available_quantity > 0"
        )->execute([$poolId]);

        return $newId;
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->pdo->prepare("UPDATE license_allocations SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function delete(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM license_allocations WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function isPoolExpired(int $poolId): bool {
        $stmt = $this->pdo->prepare("SELECT expiry_date FROM license_pools WHERE id = ?");
        $stmt->execute([$poolId]);
        $row = $stmt->fetch();
        return $row && strtotime($row['expiry_date']) < time();
    }

    public function hasAvailableSlots(int $poolId): bool {
        $stmt = $this->pdo->prepare("SELECT available_quantity FROM license_pools WHERE id = ?");
        $stmt->execute([$poolId]);
        $row = $stmt->fetch();
        return $row && $row['available_quantity'] > 0;
    }

    public function userAlreadyHasLicense(int $userId, int $softwareId): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM license_allocations
             WHERE user_id = ? AND software_id = ? AND status = 'ACTIVE'"
        );
        $stmt->execute([$userId, $softwareId]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Đếm số license ACTIVE hiện tại của một user.
     * Dùng bởi StudentAllocationStrategy để kiểm tra giới hạn 3 license.
     */
    public function countActiveByUser(int $userId): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM license_allocations
             WHERE user_id = ? AND status = 'ACTIVE'"
        );
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }
}
