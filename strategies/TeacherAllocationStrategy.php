<?php
require_once __DIR__ . '/AllocationStrategyInterface.php';

/**
 * Strategy Pattern — Teacher Allocation Strategy
 *
 * Áp dụng cho user có role = 'TEACHER'.
 * Business rules:
 * - Thời hạn tối đa 365 ngày (1 năm học)
 * - Không giới hạn số license active (khác Student)
 */
class TeacherAllocationStrategy implements AllocationStrategyInterface {

    private const DEFAULT_DURATION = 365;

    public function calculateValidUntil(int $durationDays): string {
        $duration = ($durationDays > 0 && $durationDays <= self::DEFAULT_DURATION)
            ? $durationDays
            : self::DEFAULT_DURATION;
        return date('Y-m-d H:i:s', strtotime("+{$duration} days"));
    }

    public function validate(array $data): array {
        $errors = [];

        // Business rule: duration không được vượt 365 ngày
        if (isset($data['duration_days']) && $data['duration_days'] > self::DEFAULT_DURATION) {
            $errors[] = "Thời hạn cấp phát cho Teacher không được vượt quá " . self::DEFAULT_DURATION . " ngày.";
        }

        // Teacher không bị giới hạn số lượng license active

        return $errors;
    }

    public function getRoleName(): string {
        return 'TEACHER';
    }
}
