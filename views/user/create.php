<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add User</h4>
    <a href="index.php?module=user&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">
        <form method="POST" action="index.php?module=user&action=create">
            <div class="mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" minlength="3" maxlength="50" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" maxlength="100" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="STUDENT" <?= ($_POST['role'] ?? '') === 'STUDENT' ? 'selected' : '' ?>>STUDENT</option>
                    <option value="TEACHER" <?= ($_POST['role'] ?? '') === 'TEACHER' ? 'selected' : '' ?>>TEACHER</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Department ID</label>
                <input type="text" name="department_id" class="form-control" maxlength="50" value="<?= htmlspecialchars($_POST['department_id'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
