<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Software Titles</h4>
    <a href="index.php?module=software&action=create" class="btn btn-primary btn-sm">+ Add Software</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Vendor</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($software)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">No software titles found.</td></tr>
                <?php else: ?>
                    <?php foreach ($software as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['name']) ?></td>
                        <td><?= htmlspecialchars($s['vendor']) ?></td>
                        <td class="text-end">
                            <a href="index.php?module=software&action=edit&id=<?= $s['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="index.php?module=software&action=delete&id=<?= $s['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete \'<?= htmlspecialchars($s['name']) ?>\'? This cannot be undone.')">
                               Delete
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
