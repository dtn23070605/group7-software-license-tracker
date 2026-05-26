<?php
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $model;

    public function __construct() {
        $this->model = new User();
    }

    public function index(): void {
        $users = $this->model->getAll();
        require __DIR__ . '/../views/user/index.php';
    }

    public function create(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username     = trim($_POST['username'] ?? '');
            $email        = trim($_POST['email'] ?? '');
            $role         = trim($_POST['role'] ?? '');
            $departmentId = trim($_POST['department_id'] ?? '');
            $errors       = [];

            if (empty($username)) $errors[] = "Username is required.";
            if (empty($email))    $errors[] = "Email is required.";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
            if (!in_array($role, ['STUDENT', 'TEACHER'])) $errors[] = "Role must be STUDENT or TEACHER.";

            // Business rules
            if (!empty($username) && $this->model->usernameExists($username)) {
                $errors[] = "Username already exists.";
            }
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->model->emailExists($email)) {
                $errors[] = "Email already exists.";
            }

            if (empty($errors)) {
                $this->model->create($username, $email, $role, $departmentId);
                header("Location: index.php?module=user&action=index&success=created");
                exit;
            }

            require __DIR__ . '/../views/user/create.php';
        } else {
            require __DIR__ . '/../views/user/create.php';
        }
    }

    public function edit(): void {
        $id   = (int)($_GET['id'] ?? 0);
        $user = $this->model->getById($id);
        if (!$user) {
            header("Location: index.php?module=user&action=index&error=notfound");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username     = trim($_POST['username'] ?? '');
            $email        = trim($_POST['email'] ?? '');
            $role         = trim($_POST['role'] ?? '');
            $departmentId = trim($_POST['department_id'] ?? '');
            $errors       = [];

            if (empty($username)) $errors[] = "Username is required.";
            if (empty($email))    $errors[] = "Email is required.";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format.";
            if (!in_array($role, ['STUDENT', 'TEACHER'])) $errors[] = "Role must be STUDENT or TEACHER.";

            if (!empty($username) && $this->model->usernameExists($username, $id)) {
                $errors[] = "Username already taken by another user.";
            }
            if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL) && $this->model->emailExists($email, $id)) {
                $errors[] = "Email already taken by another user.";
            }

            if (empty($errors)) {
                $this->model->update($id, $username, $email, $role, $departmentId);
                header("Location: index.php?module=user&action=index&success=updated");
                exit;
            }

            require __DIR__ . '/../views/user/edit.php';
        } else {
            require __DIR__ . '/../views/user/edit.php';
        }
    }

    public function delete(): void {
        $id = (int)($_GET['id'] ?? 0);

        if ($this->model->hasLinkedAllocations($id)) {
            header("Location: index.php?module=user&action=index&error=has_allocations");
            exit;
        }

        $this->model->delete($id);
        header("Location: index.php?module=user&action=index&success=deleted");
        exit;
    }
}
