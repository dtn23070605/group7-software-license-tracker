<?php
/**
 * Internal REST API — Check Allocation Eligibility
 *
 * GET /api/check_allocation.php?pool_id=X&user_id=Y
 *
 * Trả về JSON kiểm tra trước khi submit form allocation:
 * - Pool có hết hạn không
 * - Pool còn slot không
 * - User đã có license active cho software này chưa
 * - Strategy Pattern: user có vượt giới hạn role không (VD: Student > 3 license)
 * - Ngày hết hạn dự kiến nếu cấp phát thành công
 *
 * Dùng bởi views/allocation/create.php qua fetch() để hiển thị cảnh báo
 * ngay khi người dùng chọn pool/user, không cần submit form mới biết lỗi.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/LicenseAllocation.php';
require_once __DIR__ . '/../models/LicensePool.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/AllocationRule.php';
require_once __DIR__ . '/../strategies/AllocationStrategyInterface.php';
require_once __DIR__ . '/../strategies/StudentAllocationStrategy.php';
require_once __DIR__ . '/../strategies/TeacherAllocationStrategy.php';

function resolveStrategy(string $role): AllocationStrategyInterface {
    return match($role) {
        'TEACHER' => new TeacherAllocationStrategy(),
        default   => new StudentAllocationStrategy(),
    };
}

$poolId = (int)($_GET['pool_id'] ?? 0);
$userId = (int)($_GET['user_id'] ?? 0);

$response = [
    'valid'    => true,
    'warnings' => [],
    'preview'  => null,
];

if ($poolId <= 0 || $userId <= 0) {
    $response['valid'] = false;
    $response['warnings'][] = 'Vui lòng chọn cả Pool và User.';
    echo json_encode($response);
    exit;
}

$allocationModel = new LicenseAllocation();
$poolModel       = new LicensePool();
$userModel       = new User();
$ruleModel       = new AllocationRule();

$pool = $poolModel->getById($poolId);
$user = $userModel->getById($userId);

if (!$pool || !$user) {
    $response['valid'] = false;
    $response['warnings'][] = 'Pool hoặc User không tồn tại.';
    echo json_encode($response);
    exit;
}

$softwareId = $pool['software_id'];

// Check 1: pool hết hạn
if ($allocationModel->isPoolExpired($poolId)) {
    $response['valid'] = false;
    $response['warnings'][] = 'Pool này đã hết hạn, không thể cấp phát.';
}

// Check 2: pool còn slot
if (!$allocationModel->hasAvailableSlots($poolId)) {
    $response['valid'] = false;
    $response['warnings'][] = 'Pool này đã hết slot trống.';
}

// Check 3: user đã có license active cho software này
if ($allocationModel->userAlreadyHasLicense($userId, $softwareId)) {
    $response['valid'] = false;
    $response['warnings'][] = 'User này đã có license ACTIVE cho phần mềm này.';
}

// Check 4 (Strategy Pattern): điều kiện riêng theo role
$rule         = $ruleModel->getByRoleAndSoftware($user['role'], $softwareId);
$durationDays = $rule ? (int)$rule['duration_days'] : 0;
$strategy     = resolveStrategy($user['role']);
$activeCount  = $allocationModel->countActiveByUser($userId);

$strategyErrors = $strategy->validate([
    'duration_days' => $durationDays,
    'active_count'  => $activeCount,
]);

foreach ($strategyErrors as $err) {
    $response['valid'] = false;
    $response['warnings'][] = $err;
}

// Nếu hợp lệ, tính trước ngày hết hạn để hiển thị preview cho người dùng
if ($response['valid']) {
    $validUntil = $strategy->calculateValidUntil($durationDays);
    $response['preview'] = [
        'role'        => $user['role'],
        'valid_until' => $validUntil,
        'valid_until_display' => date('d M Y', strtotime($validUntil)),
    ];
}

echo json_encode($response);
