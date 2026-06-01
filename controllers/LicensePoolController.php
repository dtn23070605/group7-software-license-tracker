<?php
require_once __DIR__ . '/../models/LicensePool.php';
require_once __DIR__ . '/../models/SoftwareTitle.php';

class LicensePoolController {
    private $model;
    private $softwareModel;

    public function __construct() {
        $this->model         = new LicensePool();
        $this->softwareModel = new SoftwareTitle();
    }

    public function index(): void {
        $pools = $this->model->getAll();
        require __DIR__ . '/../views/pool/index.php';
    }

    public function create(): void {
        $softwareList = $this->softwareModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $softwareId   = (int)($_POST['software_id'] ?? 0);
            $totalQty     = (int)($_POST['total_quantity'] ?? 0);
            $availableQty = (int)($_POST['available_quantity'] ?? 0);
            $expiryDate   = trim($_POST['expiry_date'] ?? '');
            $errors       = [];

            if ($softwareId <= 0)   $errors[] = "Please select a software title.";
            if ($totalQty <= 0)     $errors[] = "Total quantity must be greater than 0.";
            if ($availableQty < 0)  $errors[] = "Available quantity cannot be negative.";

            // Business rule: available cannot exceed total
            if ($availableQty > $totalQty) {
                $errors[] = "Available quantity cannot exceed total quantity.";
            }

            // Business rule: expiry date must be in the future
            if (empty($expiryDate)) {
                $errors[] = "Expiry date is required.";
            } elseif (strtotime($expiryDate) <= time()) {
                $errors[] = "Expiry date must be in the future.";
            }

            if (empty($errors)) {
                $this->model->create($softwareId, $totalQty, $availableQty, $expiryDate);
                header("Location: index.php?module=pool&action=index&success=created");
                exit;
            }

            require __DIR__ . '/../views/pool/create.php';
        } else {
            require __DIR__ . '/../views/pool/create.php';
        }
    }

    public function edit(): void {
        $id           = (int)($_GET['id'] ?? 0);
        $pool         = $this->model->getById($id);
        $softwareList = $this->softwareModel->getAll();

        if (!$pool) {
            header("Location: index.php?module=pool&action=index&error=notfound");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $softwareId   = (int)($_POST['software_id'] ?? 0);
            $totalQty     = (int)($_POST['total_quantity'] ?? 0);
            $availableQty = (int)($_POST['available_quantity'] ?? 0);
            $expiryDate   = trim($_POST['expiry_date'] ?? '');
            $errors       = [];

            if ($softwareId <= 0)   $errors[] = "Please select a software title.";
            if ($totalQty <= 0)     $errors[] = "Total quantity must be greater than 0.";
            if ($availableQty < 0)  $errors[] = "Available quantity cannot be negative.";

            // Business rule: available cannot exceed total
            if ($availableQty > $totalQty) {
                $errors[] = "Available quantity cannot exceed total quantity.";
            }

            // Business rule: expiry date must be in the future
            if (empty($expiryDate)) {
                $errors[] = "Expiry date is required.";
            } elseif (strtotime($expiryDate) <= time()) {
                $errors[] = "Expiry date must be in the future.";
            }

            if (empty($errors)) {
                $this->model->update($id, $softwareId, $totalQty, $availableQty, $expiryDate);
                header("Location: index.php?module=pool&action=index&success=updated");
                exit;
            }

            require __DIR__ . '/../views/pool/edit.php';
        } else {
            require __DIR__ . '/../views/pool/edit.php';
        }
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);

        // Business rule: prevent deletion if allocations exist
        if ($this->model->hasLinkedAllocations($id)) {
            header("Location: index.php?module=pool&action=index&error=has_allocations");
            exit;
        }

        $this->model->delete($id);
        header("Location: index.php?module=pool&action=index&success=deleted");
        exit;
    }
}
