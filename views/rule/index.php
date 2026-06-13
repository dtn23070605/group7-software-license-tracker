<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Allocation Rules <span class="badge bg-secondary fs-6"><?= count($rules) ?></span></h4>
    <a href="index.php?module=rule&action=create" class="btn btn-primary btn-sm">+ Add Rule</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr><th>#</th><th>Software</th><th>Target Role</th><th>Duration (days)</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($rules)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No rules found.</td></tr>
                <?php else: foreach ($rules as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['software_name']) ?></td>
                        <td><span class="badge <?= $r['target_role'] === 'TEACHER' ? 'bg-primary' : 'bg-success' ?>"><?= $r['target_role'] ?></span></td>
                        <td><?= $r['duration_days'] ?> days</td>
                        <td class="text-end">
                            <a href="index.php?module=rule&action=edit&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="index.php?module=rule&action=delete&id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this rule?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
