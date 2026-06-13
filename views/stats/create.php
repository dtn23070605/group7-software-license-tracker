<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Usage Stat</h4>
    <a href="index.php?module=stats&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST" action="index.php?module=stats&action=create">
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
                <label class="form-label">Term Name <span class="text-danger">*</span></label>
                <input type="text" name="term_name" class="form-control" placeholder="e.g. HK1_2026" maxlength="50" value="<?= htmlspecialchars($_POST['term_name'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Allocated <span class="text-danger">*</span></label>
                <input type="number" name="total_allocated" class="form-control" min="0" value="<?= htmlspecialchars($_POST['total_allocated'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Activated <span class="text-danger">*</span></label>
                <input type="number" name="total_activated" class="form-control" min="0" value="<?= htmlspecialchars($_POST['total_activated'] ?? '') ?>" required>
                <div class="form-text">Cannot exceed total allocated. Activation rate is calculated automatically.</div>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
