<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Revoke a License</h4>
    <a href="index.php?module=revocation&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST" action="index.php?module=revocation&action=create">
            <div class="mb-3">
                <label class="form-label">Active Allocation <span class="text-danger">*</span></label>
                <select name="allocation_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($allocations as $a): if ($a['status'] !== 'ACTIVE') continue; ?>
                        <option value="<?= $a['id'] ?>" <?= ($_POST['allocation_id'] ?? '') == $a['id'] ? 'selected' : '' ?>>
                            #<?= $a['id'] ?> — <?= htmlspecialchars($a['username']) ?> / <?= htmlspecialchars($a['software_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Reason <span class="text-danger">*</span></label>
                <input type="text" name="reason" class="form-control" minlength="3" maxlength="100" value="<?= htmlspecialchars($_POST['reason'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-danger">Revoke</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
