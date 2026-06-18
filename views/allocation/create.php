<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Allocation</h4>
    <a href="index.php?module=allocation&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <div class="alert alert-info mb-3" style="font-size:0.875rem">
            ℹ️ Ngày hết hạn sẽ được tự động tính dựa trên role của user và <strong>Allocation Rules</strong> đã cấu hình.
        </div>
        <!-- Retain form values after validation error -->
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
                        <option value="<?= $u['id'] ?>" <?= ($_POST['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($u['username']) ?> (<?= $u['role'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">STUDENT: tối đa 180 ngày, tối đa 3 license. TEACHER: tối đa 365 ngày.</div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
