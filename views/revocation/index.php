<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Revocation Logs <span class="badge bg-secondary fs-6"><?= count($logs) ?></span></h4>
    <a href="index.php?module=revocation&action=create" class="btn btn-danger btn-sm">+ Revoke License</a>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr><th>#</th><th>User</th><th>Software</th><th>Reason</th><th>Revoked At</th></tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No revocation logs found.</td></tr>
                <?php else: foreach ($logs as $l): ?>
                    <tr>
                        <td><?= $l['id'] ?></td>
                        <td><?= htmlspecialchars($l['username']) ?></td>
                        <td><?= htmlspecialchars($l['software_name']) ?></td>
                        <td><?= htmlspecialchars($l['reason']) ?></td>
                        <td><?= date('d M Y H:i', strtotime($l['revoked_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
