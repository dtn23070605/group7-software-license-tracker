<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Allocation Status</h4>
    <a href="index.php?module=allocation&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <dl class="row mb-3">
            <dt class="col-sm-4">User</dt><dd class="col-sm-8"><?= htmlspecialchars($allocation['username']) ?></dd>
            <dt class="col-sm-4">Software</dt><dd class="col-sm-8"><?= htmlspecialchars($allocation['software_name']) ?></dd>
            <dt class="col-sm-4">Valid Until</dt><dd class="col-sm-8"><?= date('d M Y H:i', strtotime($allocation['valid_until'])) ?></dd>
        </dl>
        <form method="POST" action="index.php?module=allocation&action=edit&id=<?= $allocation['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select" required>
                    <option value="ACTIVE"  <?= ($_POST['status'] ?? $allocation['status']) === 'ACTIVE'  ? 'selected' : '' ?>>ACTIVE</option>
                    <option value="EXPIRED" <?= ($_POST['status'] ?? $allocation['status']) === 'EXPIRED' ? 'selected' : '' ?>>EXPIRED</option>
                    <option value="REVOKED" <?= ($_POST['status'] ?? $allocation['status']) === 'REVOKED' ? 'selected' : '' ?>>REVOKED</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
