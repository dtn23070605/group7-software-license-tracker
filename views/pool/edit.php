<?php require __DIR__ . '/../layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Edit License Pool</h4>
    <a href="index.php?module=pool&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
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
        <form method="POST" action="index.php?module=pool&action=edit&id=<?= $pool['id'] ?>">
            <div class="mb-3">
                <label class="form-label">Software Title <span class="text-danger">*</span></label>
                <select name="software_id" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($softwareList as $s): ?>
                        <option value="<?= $s['id'] ?>"
                            <?= (($_POST['software_id'] ?? $pool['software_id']) == $s['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['vendor']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Total Quantity <span class="text-danger">*</span></label>
                <input type="number" name="total_quantity" class="form-control" min="1"
                       value="<?= htmlspecialchars($_POST['total_quantity'] ?? $pool['total_quantity']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Available Quantity <span class="text-danger">*</span></label>
                <input type="number" name="available_quantity" class="form-control" min="0"
                       value="<?= htmlspecialchars($_POST['available_quantity'] ?? $pool['available_quantity']) ?>" required>
                <div class="form-text">Cannot exceed total quantity.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                <?php
                    $expiryVal = $_POST['expiry_date'] ?? date('Y-m-d\TH:i', strtotime($pool['expiry_date']));
                ?>
                <input type="datetime-local" name="expiry_date" class="form-control"
                       value="<?= htmlspecialchars($expiryVal) ?>" required>
                <div class="form-text">Must be a future date.</div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<?php require __DIR__ . '/../layout/footer.php'; ?>
