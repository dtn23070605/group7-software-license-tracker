<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Users <span class="badge bg-secondary fs-6"><?= count($users) ?></span></h4>
    <a href="index.php?module=user&action=create" class="btn btn-primary btn-sm">+ Add User</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr><th>#</th><th>Username</th><th>Email</th><th>Role</th><th>Department</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                <?php else: foreach ($users as $u): ?>
                    <tr>
                        <td><?= $u['id'] ?></td>
                        <td><?= htmlspecialchars($u['username']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><span class="badge <?= $u['role'] === 'TEACHER' ? 'bg-primary' : 'bg-success' ?>"><?= $u['role'] ?></span></td>
                        <td><?= htmlspecialchars($u['department_id'] ?? '-') ?></td>
                        <td class="text-end">
                            <a href="index.php?module=user&action=edit&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="index.php?module=user&action=delete&id=<?= $u['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete user \'<?= htmlspecialchars($u['username']) ?>\'?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
