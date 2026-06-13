<?php
require_once __DIR__ . '/../models/UsageStat.php';
require_once __DIR__ . '/../models/SoftwareTitle.php';

class UsageStatController {
    private $model;
    private $softwareModel;

    public function __construct() {
        $this->model         = new UsageStat();
        $this->softwareModel = new SoftwareTitle();
    }

    public function index(): void {
        $stats        = $this->model->getAll();
        $terms        = $this->model->getDistinctTerms();
        $filterTerm   = $_GET['term'] ?? '';
        if ($filterTerm) {
            $stats = $this->model->getByTerm($filterTerm);
        }
        require __DIR__ . '/../views/stats/index.php';
    }

    public function create(): void {
        $softwareList = $this->softwareModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $softwareId      = (int)($_POST['software_id'] ?? 0);
            $termName        = trim($_POST['term_name'] ?? '');
            $totalAllocated  = (int)($_POST['total_allocated'] ?? 0);
            $totalActivated  = (int)($_POST['total_activated'] ?? 0);
            $errors          = [];

            if ($softwareId <= 0)      $errors[] = "Please select a software title.";
            if (empty($termName))      $errors[] = "Term name is required.";
            if ($totalAllocated < 0)   $errors[] = "Total allocated cannot be negative.";
            if ($totalActivated < 0)   $errors[] = "Total activated cannot be negative.";

            // Business rule: activated cannot exceed allocated
            if ($totalActivated > $totalAllocated) {
                $errors[] = "Total activated cannot exceed total allocated.";
            }

            if (empty($errors)) {
                $this->model->create($softwareId, $termName, $totalAllocated, $totalActivated);
                header("Location: index.php?module=stats&action=index&success=created");
                exit;
            }

            require __DIR__ . '/../views/stats/create.php';
        } else {
            require __DIR__ . '/../views/stats/create.php';
        }
    }
}
