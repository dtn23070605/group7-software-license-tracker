<?php
require_once __DIR__ . '/../models/SoftwareTitle.php';

class SoftwareTitleController {
    private $model;

    public function __construct() {
        $this->model = new SoftwareTitle();
    }

    public function index(): void {
        $software = $this->model->getAll();
        require __DIR__ . '/../views/software/index.php';
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name   = trim($_POST['name'] ?? '');
            $vendor = trim($_POST['vendor'] ?? '');
            $errors = [];

            // Validation
            if (empty($name))   $errors[] = "Software name is required.";
            if (empty($vendor)) $errors[] = "Vendor name is required.";

            // Business rule: software name must be unique across all titles
            if (!empty($name) && $this->model->nameExists($name)) {
                $errors[] = "A software title with this name already exists.";
            }

            if (empty($errors)) {
                $this->model->create($name, $vendor);
                header("Location: index.php?module=software&action=index&success=created");
                exit;
            }

            require __DIR__ . '/../views/software/create.php';
        } else {
            require __DIR__ . '/../views/software/create.php';
        }
    }

    public function edit(): void {
        $id = (int)($_GET['id'] ?? 0);
        $software = $this->model->getById($id);
        if (!$software) {
            header("Location: index.php?module=software&action=index&error=notfound");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name   = trim($_POST['name'] ?? '');
            $vendor = trim($_POST['vendor'] ?? '');
            $errors = [];

            if (empty($name))   $errors[] = "Software name is required.";
            if (empty($vendor)) $errors[] = "Vendor name is required.";

            // Business rule: no duplicate names excluding current record
            if (!empty($name) && $this->model->nameExists($name, $id)) {
                $errors[] = "Another software title with this name already exists.";
            }

            if (empty($errors)) {
                $this->model->update($id, $name, $vendor);
                header("Location: index.php?module=software&action=index&success=updated");
                exit;
            }

            require __DIR__ . '/../views/software/edit.php';
        } else {
            require __DIR__ . '/../views/software/edit.php';
        }
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);

        // Business rule: prevent deletion if pools exist
        if ($this->model->hasLinkedPools($id)) {
            header("Location: index.php?module=software&action=index&error=has_pools");
            exit;
        }

        $this->model->delete($id);
        header("Location: index.php?module=software&action=index&success=deleted");
        exit;
    }
}
