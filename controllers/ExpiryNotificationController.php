<?php
require_once __DIR__ . '/../auth/Auth.php';
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
        // RBAC: chỉ Admin/Teacher xem được thông báo hết hạn của toàn hệ thống
        Auth::requireAdmin();

        $notifications = $this->model->getAll();

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

        $notifications = $this->model->getAll();
        require __DIR__ . '/../views/expiry/index.php';
    }
}
