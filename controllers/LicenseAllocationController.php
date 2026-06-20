<?php
require_once __DIR__ . '/../auth/Auth.php';
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
     */
    private function resolveStrategy(string $role): AllocationStrategyInterface {
        return match($role) {
            'TEACHER' => new TeacherAllocationStrategy(),
            default   => new StudentAllocationStrategy(),
        };
    }

    /**
     * RBAC: Admin (TEACHER) thấy toàn bộ allocations.
     * Student chỉ thấy allocations của chính mình.
     */
    public function index(): void {
        if (Auth::isAdmin()) {
            $allocations = $this->model->getAll();
        } else {
            $allocations = $this->model->getAllByUser(Auth::getUserId());
        }
        require __DIR__ . '/../views/allocation/index.php';
    }

    /**
     * RBAC: Admin chọn pool + user tự do (cấp phát cho bất kỳ ai).
     * Student chỉ được tự cấp phát cho chính mình — không chọn user khác.
     */
    public function create(): void {
        $pools = $this->poolModel->getAll();
        $users = Auth::isAdmin() ? $this->userModel->getAll() : [$this->userModel->getById(Auth::getUserId())];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $poolId = (int)($_POST['pool_id'] ?? 0);

            // RBAC: Student không được chọn user khác — luôn dùng user đang đăng nhập
            $userId = Auth::isAdmin() ? (int)($_POST['user_id'] ?? 0) : Auth::getUserId();

            $errors = [];

            if ($poolId <= 0) $errors[] = "Please select a license pool.";
            if ($userId <= 0) $errors[] = "Please select a user.";

            $pool = $poolId > 0 ? $this->poolModel->getById($poolId) : null;
            $user = $userId > 0 ? $this->userModel->getById($userId) : null;

            if ($pool && $user) {
                $softwareId = $pool['software_id'];

                if ($this->model->isPoolExpired($poolId)) {
                    $errors[] = "Cannot allocate from an expired license pool.";
                }
                if (!$this->model->hasAvailableSlots($poolId)) {
                    $errors[] = "No available slots in this license pool.";
                }
                if ($this->model->userAlreadyHasLicense($userId, $softwareId)) {
                    $errors[] = "This user already has an active license for this software.";
                }

                $rule         = $this->ruleModel->getByRoleAndSoftware($user['role'], $softwareId);
                $durationDays = $rule ? (int)$rule['duration_days'] : 0;
                $strategy     = $this->resolveStrategy($user['role']);
                $activeCount  = $this->model->countActiveByUser($userId);

                $strategyErrors = $strategy->validate([
                    'duration_days' => $durationDays,
                    'active_count'  => $activeCount,
                ]);
                $errors = array_merge($errors, $strategyErrors);

                if (empty($errors)) {
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

    /**
     * RBAC: chỉ Admin được đổi status. Student không có quyền edit.
     */
    public function edit(): void {
        Auth::requireAdmin();

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

    /**
     * RBAC: chỉ Admin được xóa allocation.
     */
    public function delete(): void {
        Auth::requireAdmin();

        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        header("Location: index.php?module=allocation&action=index&success=deleted");
        exit;
    }
}
