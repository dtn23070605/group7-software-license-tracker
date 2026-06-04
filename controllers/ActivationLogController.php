<?php
require_once __DIR__ . '/../models/ActivationLog.php';

class ActivationLogController {
    private $model;

    public function __construct() {
        $this->model = new ActivationLog();
    }

    public function index(): void {
        $logs = $this->model->getAll();
        require __DIR__ . '/../views/activation/index.php';
    }
}
