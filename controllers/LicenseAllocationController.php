<?php
require_once __DIR__ . '/../models/LicenseAllocation.php';
require_once __DIR__ . '/../models/LicensePool.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/SoftwareTitle.php';
require_once __DIR__ . '/../models/ActivationLog.php';
require_once __DIR__ . '/../models/AllocationRule.php';
require_once __DIR__ . '/../strategies/AllocationStrategyInterface.php';
require_once __DIR__ . '/../strategies/StudentAllocationStrategy.php';
require_once __DIR__ . '/../strategies/TeacherAllocationStrategy.php';

class LicenseAllocationController {
    private $model;
    private $poolModel;
    private $userModel;
    private $softwareModel;
    private $activationLog;
    private $ruleModel;

    public function __construct() {
        $this->model         = new LicenseAllocation();
        $this->poolModel     = new LicensePool();
        $this->userModel     = new User();
        $this->softwareModel = new SoftwareTitle();
        $this->activationLog = new ActivationLog();
        $this->ruleModel     = new AllocationRule();
    }

    /**
     * Strategy Pattern — chọn strategy dựa trên role của user.
     * Thêm role mới: chỉ cần tạo class mới và thêm case vào đây.
     */
    private function resolveStrategy(string $role): AllocationStrategyInterface {
        return match($role) {
            'TEACHER' => new TeacherAllocationStrategy(),
            default   => new StudentAllocationStrategy(),
        };
    }

    public function index(): void {
        $allocations = $this->model->getAll();
        require __DIR__ . '/../views/allocation/index.php';
    }

    public function create(): void {
        $pools = $this->poolModel->getAll();
        $users = $this->userModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $poolId = (int)($_POST['pool_id'] ?? 0);
            $userId = (int)($_POST['user_id'] ?? 0);
            $errors = [];

            if ($poolId <= 0) $errors[] = "Please select a license pool.";
            if ($userId <= 0) $errors[] = "Please select a user.";

            $pool = $poolId > 0 ? $this->poolModel->getById($poolId) : null;
            $user = $userId > 0 ? $this->userModel->getById($userId) : null;

            if ($pool && $user) {
                $softwareId = $pool['software_id'];

                // Business rule: không cấp từ pool đã hết hạn
                if ($this->model->isPoolExpired($poolId)) {
                    $errors[] = "Cannot allocate from an expired license pool.";
                }

                // Business rule: pool phải còn slot
                if (!$this->model->hasAvailableSlots($poolId)) {
                    $errors[] = "No available slots in this license pool.";
                }

                // Business rule: user không được có 2 license active cho cùng software
                if ($this->model->userAlreadyHasLicense($userId, $softwareId)) {
                    $errors[] = "This user already has an active license for this software.";
                }

                // Strategy Pattern: lấy duration từ allocation_rules theo role + software
                $rule         = $this->ruleModel->getByRoleAndSoftware($user['role'], $softwareId);
                $durationDays = $rule ? (int)$rule['duration_days'] : 0;

                // Strategy Pattern: resolve strategy theo role của user
                $strategy = $this->resolveStrategy($user['role']);

                // Đếm số license active hiện tại (dùng bởi StudentAllocationStrategy)
                $activeCount = $this->model->countActiveByUser($userId);

                // Validate theo rule của từng role
                $strategyErrors = $strategy->validate([
                    'duration_days' => $durationDays,
                    'active_count'  => $activeCount,
                ]);
                $errors = array_merge($errors, $strategyErrors);

                if (empty($errors)) {
                    // Strategy tự tính valid_until — không cần user nhập
                    $validUntil = $strategy->calculateValidUntil($durationDays);

                    // create() trả về ID vừa tạo (false nếu thất bại)
                    $newAllocationId = $this->model->create($poolId, $softwareId, $userId, $validUntil);

                    if ($newAllocationId !== false) {
                        $this->activationLog->log($newAllocationId);
                    }

                    header("Location: index.php?module=allocation&action=index&success=created");
                    exit;
                }
            }

            require __DIR__ . '/../views/allocation/create.php';
        } else {
            require __DIR__ . '/../views/allocation/create.php';
        }
    }

    public function edit(): void {
        $id         = (int)($_GET['id'] ?? 0);
        $allocation = $this->model->getById($id);
        if (!$allocation) {
            header("Location: index.php?module=allocation&action=index&error=notfound");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = trim($_POST['status'] ?? '');
            $errors = [];

            if (!in_array($status, ['ACTIVE', 'EXPIRED', 'REVOKED'])) {
                $errors[] = "Invalid status value.";
            }

            if (empty($errors)) {
                $this->model->updateStatus($id, $status);
                header("Location: index.php?module=allocation&action=index&success=updated");
                exit;
            }

            require __DIR__ . '/../views/allocation/edit.php';
        } else {
            require __DIR__ . '/../views/allocation/edit.php';
        }
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        header("Location: index.php?module=allocation&action=index&success=deleted");
        exit;
    }
}
