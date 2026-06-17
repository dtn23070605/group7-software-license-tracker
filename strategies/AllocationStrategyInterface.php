<?php

/**
 * Strategy Pattern — Allocation Strategy Interface
 *
 * Mỗi role (STUDENT, TEACHER) có một strategy riêng xác định:
 * - Thời hạn cấp phát mặc định (duration)
 * - Điều kiện hợp lệ trước khi cấp phát
 *
 * Khi thêm role mới, chỉ cần tạo class mới implement interface này
 * mà không cần sửa controller.
 */
interface AllocationStrategyInterface {

    /**
     * Tự động tính valid_until dựa trên duration_days của rule.
     * Trả về chuỗi datetime 'Y-m-d H:i:s'.
     */
    public function calculateValidUntil(int $durationDays): string;

    /**
     * Kiểm tra điều kiện nghiệp vụ riêng của role.
     * Trả về mảng lỗi — rỗng nếu hợp lệ.
     */
    public function validate(array $data): array;

    /**
     * Trả về tên role.
     */
    public function getRoleName(): string;
}
