<?php
require_once __DIR__ . '/AllocationStrategyInterface.php';

/**
 * Strategy Pattern — Student Allocation Strategy
 *
 * Áp dụng cho user có role = 'STUDENT'.
 * Business rules:
 * - Thời hạn tối đa 180 ngày (1 học kỳ)
 * - Không được giữ quá 3 license ACTIVE cùng lúc
 */
class StudentAllocationStrategy implements AllocationStrategyInterface {

    private const DEFAULT_DURATION  = 180;
    private const MAX_ACTIVE_LICENSES = 3;

    public function calculateValidUntil(int $durationDays): string {
        // Dùng duration từ allocation_rules nếu có, fallback về mặc định 180 ngày
        $duration = ($durationDays > 0 && $durationDays <= self::DEFAULT_DURATION)
            ? $durationDays
            : self::DEFAULT_DURATION;
        return date('Y-m-d H:i:s', strtotime("+{$duration} days"));
    }

    public function validate(array $data): array {
        $errors = [];

        // Business rule: Student không được giữ quá 3 license active cùng lúc
        if (isset($data['active_count']) && $data['active_count'] >= self::MAX_ACTIVE_LICENSES) {
            $errors[] = "Student đã đạt giới hạn " . self::MAX_ACTIVE_LICENSES . " license active cùng lúc.";
        }

        // Business rule: duration không được vượt 180 ngày
        if (isset($data['duration_days']) && $data['duration_days'] > self::DEFAULT_DURATION) {
            $errors[] = "Thời hạn cấp phát cho Student không được vượt quá " . self::DEFAULT_DURATION . " ngày.";
        }

        return $errors;
    }

    public function getRoleName(): string {
        return 'STUDENT';
    }
}
