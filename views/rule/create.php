<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Allocation Rule</h4>
    <a href="index.php?module=rule&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST" action="index.php?module=rule&action=create">
            <div class="mb-3">
                <label class="form-label">Software Title <span class="text-danger">*</span></label>
                <select name="software_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($softwareList as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= ($_POST['software_id'] ?? '') == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Target Role <span class="text-danger">*</span></label>
                <select name="target_role" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="STUDENT" <?= ($_POST['target_role'] ?? '') === 'STUDENT' ? 'selected' : '' ?>>STUDENT</option>
                    <option value="TEACHER" <?= ($_POST['target_role'] ?? '') === 'TEACHER' ? 'selected' : '' ?>>TEACHER</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Duration (days) <span class="text-danger">*</span></label>
                <input type="number" name="duration_days" class="form-control" min="1" max="365" value="<?= htmlspecialchars($_POST['duration_days'] ?? '') ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
