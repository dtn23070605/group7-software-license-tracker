<?php
require_once __DIR__ . '/../models/RevocationLog.php';
require_once __DIR__ . '/../models/LicenseAllocation.php';

class RevocationLogController {
    private $model;
    private $allocationModel;

    public function __construct() {
        $this->model           = new RevocationLog();
        $this->allocationModel = new LicenseAllocation();
    }

    public function index(): void {
        $logs = $this->model->getAll();
        require __DIR__ . '/../views/revocation/index.php';
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $allocationId = (int)($_POST['allocation_id'] ?? 0);
            $reason       = trim($_POST['reason'] ?? '');
            $errors       = [];

            if ($allocationId <= 0) $errors[] = "Please select an allocation.";
            if (empty($reason))     $errors[] = "Reason is required.";

            // Business rule: can only revoke ACTIVE allocations
            $allocation = $this->allocationModel->getById($allocationId);
            if ($allocation && $allocation['status'] !== 'ACTIVE') {
                $errors[] = "Only ACTIVE allocations can be revoked.";
            }

            if (empty($errors)) {
                $this->model->create($allocationId, $reason);
                $this->allocationModel->updateStatus($allocationId, 'REVOKED');
                header("Location: index.php?module=revocation&action=index&success=created");
                exit;
            }

            $allocations = $this->allocationModel->getAll();
            require __DIR__ . '/../views/revocation/create.php';
        } else {
            $allocations = $this->allocationModel->getAll();
            require __DIR__ . '/../views/revocation/create.php';
        }
    }
}
