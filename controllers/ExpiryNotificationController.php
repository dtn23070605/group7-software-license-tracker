<?php
require_once __DIR__ . '/../models/ExpiryNotification.php';
require_once __DIR__ . '/../models/LicenseAllocation.php';

class ExpiryNotificationController {
    private $model;
    private $allocationModel;

    public function __construct() {
        $this->model           = new ExpiryNotification();
        $this->allocationModel = new LicenseAllocation();
    }

    public function index(): void {
        $notifications = $this->model->getAll();

        // Business rule: auto-detect allocations expiring in 7 days or 1 day and log if not already sent
        $allocations = $this->allocationModel->getAll();
        foreach ($allocations as $a) {
            if ($a['status'] !== 'ACTIVE') continue;

            $daysLeft = (strtotime($a['valid_until']) - time()) / 86400;

            if ($daysLeft <= 7 && $daysLeft > 1) {
                if (!$this->model->alreadySent($a['id'], '7_DAYS')) {
                    $this->model->create($a['id'], '7_DAYS');
                }
            } elseif ($daysLeft <= 1 && $daysLeft > 0) {
                if (!$this->model->alreadySent($a['id'], '1_DAY')) {
                    $this->model->create($a['id'], '1_DAY');
                }
            }
        }

        // Reload after auto-logging
        $notifications = $this->model->getAll();
        require __DIR__ . '/../views/expiry/index.php';
    }
}
