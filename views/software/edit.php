<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit Software Title</h4>
    <a href="index.php?module=software&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card shadow-sm" style="max-width: 500px;">
    <div class="card-body">
        <form method="POST" action="index.php?module=software&action=edit&id=<?= $software['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Software Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="<?= htmlspecialchars($_POST['name'] ?? $software['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Vendor <span class="text-danger">*</span></label>
                <input type="text" name="vendor" class="form-control"
                       value="<?= htmlspecialchars($_POST['vendor'] ?? $software['vendor']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
