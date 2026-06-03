<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Expiry Notifications <span class="badge bg-secondary fs-6"><?= count($notifications) ?></span></h4>
</div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr><th>#</th><th>User</th><th>Software</th><th>Type</th><th>Valid Until</th><th>Sent At</th></tr>
            </thead>
            <tbody>
                <?php if (empty($notifications)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No notifications found.</td></tr>
                <?php else: foreach ($notifications as $n): ?>
                    <tr>
                        <td><?= $n['id'] ?></td>
                        <td><?= htmlspecialchars($n['username']) ?></td>
                        <td><?= htmlspecialchars($n['software_name']) ?></td>
                        <td><span class="badge <?= $n['notification_type'] === '1_DAY' ? 'bg-danger' : 'bg-warning text-dark' ?>"><?= $n['notification_type'] ?></span></td>
                        <td><?= date('d M Y', strtotime($n['valid_until'])) ?></td>
                        <td><?= date('d M Y H:i', strtotime($n['sent_at'])) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
