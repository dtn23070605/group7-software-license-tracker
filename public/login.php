<?php
require_once __DIR__ . '/../auth/Auth.php';
require_once __DIR__ . '/../models/User.php';

// Đã đăng nhập rồi thì vào thẳng dashboard
if (Auth::isLoggedIn()) {
    header("Location: index.php?module=allocation&action=index");
    exit;
}

$userModel = new User();
$users     = $userModel->getAll();
$error     = $_GET['error'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $user   = $userModel->getById($userId);

    if ($user) {
        Auth::login((int)$user['id'], $user['username'], $user['role']);
        header("Location: index.php?module=allocation&action=index");
        exit;
    } else {
        header("Location: login.php?error=invalid");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Software License Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { max-width: 420px; width: 100%; }
    </style>
</head>
<body>
    <div class="card shadow-sm login-card">
        <div class="card-body p-4">
            <h4 class="mb-1 text-center">🔑 License Tracker</h4>
            <p class="text-muted text-center mb-4" style="font-size:0.85rem">Chọn user để đăng nhập (demo, không cần password)</p>

            <?php if ($error === 'invalid'): ?>
                <div class="alert alert-danger">User không hợp lệ.</div>
            <?php endif; ?>
            <?php if (($_GET['error'] ?? '') === 'forbidden'): ?>
                <div class="alert alert-warning">Bạn không có quyền truy cập trang đó.</div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label class="form-label">Chọn tài khoản</label>
                    <select name="user_id" class="form-select" required>
                        <option value="">-- Select user --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>">
                                <?= htmlspecialchars($u['username']) ?> — <?= $u['role'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">TEACHER đăng nhập với quyền Admin. STUDENT chỉ thấy license của chính mình.</div>
                </div>
                <button type="submit" class="btn btn-primary w-100">Đăng nhập</button>
            </form>
        </div>
    </div>
</body>
</html>
