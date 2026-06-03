<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">License Allocations <span class="badge bg-secondary fs-6"><?= count($allocations) ?></span></h4>
    <a href="index.php?module=allocation&action=create" class="btn btn-primary btn-sm">+ Add Allocation</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr><th>#</th><th>User</th><th>Software</th><th>Valid Until</th><th>Status</th><th class="text-end">Actions</th></tr>
            </thead>
            <tbody>
                <?php if (empty($allocations)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No allocations found.</td></tr>
                <?php else: foreach ($allocations as $a):
                    $badgeClass = match($a['status']) { 'ACTIVE' => 'bg-success', 'EXPIRED' => 'bg-secondary', 'REVOKED' => 'bg-danger', default => 'bg-secondary' };
                ?>
                    <tr>
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['username']) ?></td>
                        <td><?= htmlspecialchars($a['software_name']) ?></td>
                        <td><?= date('d M Y', strtotime($a['valid_until'])) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= $a['status'] ?></span></td>
                        <td class="text-end">
                            <a href="index.php?module=allocation&action=edit&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="index.php?module=allocation&action=delete&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this allocation?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
