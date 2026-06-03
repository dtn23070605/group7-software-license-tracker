<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Allocation</h4>
    <a href="index.php?module=allocation&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST" action="index.php?module=allocation&action=create">
            <div class="mb-3">
                <label class="form-label">License Pool <span class="text-danger">*</span></label>
                <select name="pool_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($pools as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($_POST['pool_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['software_name']) ?> (<?= $p['available_quantity'] ?> available, expires <?= date('d M Y', strtotime($p['expiry_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">User <span class="text-danger">*</span></label>
                <select name="user_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= ($_POST['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>><?= htmlspecialchars($u['username']) ?> (<?= $u['role'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Valid Until <span class="text-danger">*</span></label>
                <input type="datetime-local" name="valid_until" class="form-control" value="<?= htmlspecialchars($_POST['valid_until'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
