<?php
require_once __DIR__ . '/../../auth/Auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software License Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background-color: #212529; display: flex; flex-direction: column; }
        .sidebar a { color: #adb5bd; text-decoration: none; display: block; padding: 8px 20px; font-size: 0.9rem; }
        .sidebar a:hover, .sidebar a.active { background-color: #343a40; color: #fff; }
        .sidebar h6 { color: #6c757d; padding: 16px 20px 4px; font-size: 0.68rem; text-transform: uppercase; letter-spacing: 1px; margin: 0; }
        .main-content { padding: 30px; }
        .user-box { margin-top: auto; padding: 14px 20px; border-top: 1px solid #343a40; }
    </style>
</head>
<body>
<div class="container-fluid">
<div class="row">
    <div class="col-md-2 sidebar p-0">
        <div class="p-3 border-bottom border-secondary">
            <span class="text-white fw-bold">🔑 License Tracker</span>
        </div>
        <?php $mod = $_GET['module'] ?? 'software'; ?>

        <?php if (Auth::isAdmin()): ?>
            <h6>Member 1 — Catalog</h6>
            <a href="index.php?module=software&action=index"  class="<?= $mod === 'software'  ? 'active' : '' ?>">📦 Software Titles</a>
            <a href="index.php?module=user&action=index"      class="<?= $mod === 'user'      ? 'active' : '' ?>">👤 Users</a>
            <a href="index.php?module=rule&action=index"      class="<?= $mod === 'rule'      ? 'active' : '' ?>">📋 Allocation Rules</a>
            <h6>Member 2 — Pools & Allocations</h6>
            <a href="index.php?module=pool&action=index"       class="<?= $mod === 'pool'       ? 'active' : '' ?>">🗃️ License Pools</a>
            <a href="index.php?module=allocation&action=index" class="<?= $mod === 'allocation' ? 'active' : '' ?>">🔗 Allocations</a>
            <a href="index.php?module=activation&action=index" class="<?= $mod === 'activation' ? 'active' : '' ?>">⚡ Activation Logs</a>
            <h6>Member 3 — Reports</h6>
            <a href="index.php?module=expiry&action=index"     class="<?= $mod === 'expiry'     ? 'active' : '' ?>">🔔 Expiry Notifications</a>
            <a href="index.php?module=revocation&action=index" class="<?= $mod === 'revocation' ? 'active' : '' ?>">🚫 Revocation Logs</a>
            <a href="index.php?module=stats&action=index"      class="<?= $mod === 'stats'      ? 'active' : '' ?>">📊 Usage Stats</a>
        <?php else: ?>
            <h6>My Account</h6>
            <a href="index.php?module=allocation&action=index" class="<?= $mod === 'allocation' ? 'active' : '' ?>">🔗 My Licenses</a>
        <?php endif; ?>

        <div class="user-box">
            <div class="text-white" style="font-size:0.85rem"><?= htmlspecialchars(Auth::getUsername() ?? '') ?></div>
            <div class="text-secondary mb-2" style="font-size:0.75rem"><?= htmlspecialchars(Auth::getRole() ?? '') ?></div>
            <a href="logout.php" class="btn btn-sm btn-outline-light w-100" style="padding:4px">Đăng xuất</a>
        </div>
    </div>
    <div class="col-md-10 main-content">
        <?php
        $success = $_GET['success'] ?? '';
        $error   = $_GET['error'] ?? '';
        $successMessages = ['created'=>'Record created successfully.','updated'=>'Record updated successfully.','deleted'=>'Record deleted successfully.'];
        $errorMessages   = ['notfound'=>'Record not found.','has_pools'=>'Cannot delete: this software title has linked license pools.','has_allocations'=>'Cannot delete: this record has linked allocations.','forbidden'=>'Bạn không có quyền truy cập trang đó.'];
        ?>
        <?php if ($success && isset($successMessages[$success])): ?>
            <div class="alert alert-success alert-dismissible fade show"><?= $successMessages[$success] ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
        <?php if ($error && isset($errorMessages[$error])): ?>
            <div class="alert alert-danger alert-dismissible fade show"><?= $errorMessages[$error] ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        <?php endif; ?>
