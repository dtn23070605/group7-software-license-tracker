<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">License Pools</h4>
    <a href="index.php?module=pool&action=create" class="btn btn-primary btn-sm">+ Add Pool</a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Software</th>
                    <th>Total</th>
                    <th>Available</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pools)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">No license pools found.</td></tr>
                <?php else: ?>
                    <?php foreach ($pools as $p):
                        $expired = strtotime($p['expiry_date']) < time();
                    ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['software_name']) ?></td>
                        <td><?= $p['total_quantity'] ?></td>
                        <td><?= $p['available_quantity'] ?></td>
                        <td><?= date('d M Y', strtotime($p['expiry_date'])) ?></td>
                        <td>
                            <?php if ($expired): ?>
                                <span class="badge bg-danger">Expired</span>
                            <?php else: ?>
                                <span class="badge bg-success">Active</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end">
                            <a href="index.php?module=pool&action=edit&id=<?= $p['id'] ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
                            <a href="index.php?module=pool&action=delete&id=<?= $p['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this license pool? This cannot be undone.')">
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
