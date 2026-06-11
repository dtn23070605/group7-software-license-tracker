<?php
require_once __DIR__ . '/../models/AllocationRule.php';
require_once __DIR__ . '/../models/SoftwareTitle.php';

class AllocationRuleController {
    private $model;
    private $softwareModel;

    public function __construct() {
        $this->model         = new AllocationRule();
        $this->softwareModel = new SoftwareTitle();
    }

    public function index(): void {
        $rules = $this->model->getAll();
        require __DIR__ . '/../views/rule/index.php';
    }

    public function create(): void {
        $softwareList = $this->softwareModel->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $softwareId   = (int)($_POST['software_id'] ?? 0);
            $targetRole   = trim($_POST['target_role'] ?? '');
            $durationDays = (int)($_POST['duration_days'] ?? 0);
            $errors       = [];

            if ($softwareId <= 0) $errors[] = "Please select a software title.";
            if (!in_array($targetRole, ['STUDENT', 'TEACHER'])) $errors[] = "Target role must be STUDENT or TEACHER.";
            if ($durationDays <= 0) $errors[] = "Duration must be greater than 0 days.";

            // Business rule: one rule per software + role combination
            if ($softwareId > 0 && !empty($targetRole) && $this->model->ruleExists($softwareId, $targetRole)) {
                $errors[] = "A rule for this software and role already exists.";
            }

            if (empty($errors)) {
                $this->model->create($softwareId, $targetRole, $durationDays);
                header("Location: index.php?module=rule&action=index&success=created");
                exit;
            }

            require __DIR__ . '/../views/rule/create.php';
        } else {
            require __DIR__ . '/../views/rule/create.php';
        }
    }

    public function edit(): void {
        $id           = (int)($_GET['id'] ?? 0);
        $rule         = $this->model->getById($id);
        $softwareList = $this->softwareModel->getAll();

        if (!$rule) {
            header("Location: index.php?module=rule&action=index&error=notfound");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $softwareId   = (int)($_POST['software_id'] ?? 0);
            $targetRole   = trim($_POST['target_role'] ?? '');
            $durationDays = (int)($_POST['duration_days'] ?? 0);
            $errors       = [];

            if ($softwareId <= 0) $errors[] = "Please select a software title.";
            if (!in_array($targetRole, ['STUDENT', 'TEACHER'])) $errors[] = "Target role must be STUDENT or TEACHER.";
            if ($durationDays <= 0) $errors[] = "Duration must be greater than 0 days.";

            if ($softwareId > 0 && !empty($targetRole) && $this->model->ruleExists($softwareId, $targetRole, $id)) {
                $errors[] = "Another rule for this software and role already exists.";
            }

            if (empty($errors)) {
                $this->model->update($id, $softwareId, $targetRole, $durationDays);
                header("Location: index.php?module=rule&action=index&success=updated");
                exit;
            }

            require __DIR__ . '/../views/rule/edit.php';
        } else {
            require __DIR__ . '/../views/rule/edit.php';
        }
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);
        $this->model->delete($id);
        header("Location: index.php?module=rule&action=index&success=deleted");
        exit;
    }
}
