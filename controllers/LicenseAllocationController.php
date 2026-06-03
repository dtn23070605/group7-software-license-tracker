<?php
require_once __DIR__ . '/../models/LicenseAllocation.php';
require_once __DIR__ . '/../models/LicensePool.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/SoftwareTitle.php';
require_once __DIR__ . '/../models/ActivationLog.php';

class LicenseAllocationController {
    private $model;
    private $poolModel;
    private $userModel;
    private $softwareModel;
    private $activationLog;

    public function __construct() {
        $this->model         = new LicenseAllocation();
        $this->poolModel     = new LicensePool();
        $this->userModel     = new User();
        $this->softwareModel = new SoftwareTitle();
        $this->activationLog = new ActivationLog();
    }

    public function index(): void {
        $allocations = $this->model->getAll();
        require __DIR__ . '/../views/allocation/index.php';
    }

    public function create(): void {
        $pools = $this->poolModel->getAll();
        $users = $this->userModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $poolId     = (int)($_POST['pool_id'] ?? 0);
            $userId     = (int)($_POST['user_id'] ?? 0);
            $validUntil = trim($_POST['valid_until'] ?? '');
            $errors     = [];

            if ($poolId <= 0) $errors[] = "Please select a license pool.";
            if ($userId <= 0) $errors[] = "Please select a user.";
            if (empty($validUntil)) $errors[] = "Valid until date is required.";
            if (!empty($validUntil) && strtotime($validUntil) <= time()) {
                $errors[] = "Valid until date must be in the future.";
            }

            if ($poolId > 0) {
                // Business rule: cannot allocate from expired pool
                if ($this->model->isPoolExpired($poolId)) {
                    $errors[] = "Cannot allocate from an expired license pool.";
                }
                // Business rule: must have available slots
                if (!$this->model->hasAvailableSlots($poolId)) {
                    $errors[] = "No available slots in this license pool.";
                }
            }

            // Get software_id from pool
            $pool = $this->poolModel->getById($poolId);
            $softwareId = $pool ? $pool['software_id'] : 0;

            // Business rule: user cannot have duplicate active license for same software
            if ($userId > 0 && $softwareId > 0 && $this->model->userAlreadyHasLicense($userId, $softwareId)) {
                $errors[] = "This user already has an active license for this software.";
            }

            if (empty($errors)) {
                $this->model->create($poolId, $softwareId, $userId, $validUntil);
                // Log activation
                $pdo  = Database::getInstance()->getConnection();
                $last = $pdo->lastInsertId();
                $this->activationLog->log((int)$last);
                header("Location: index.php?module=allocation&action=index&success=created");
                exit;
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
