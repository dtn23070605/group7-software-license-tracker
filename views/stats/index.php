<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Usage Stats</h4>
    <a href="index.php?module=stats&action=create" class="btn btn-primary btn-sm">+ Add Stat</a>
</div>
<?php if (!empty($terms)): ?>
<form method="GET" action="index.php" class="mb-3 d-flex gap-2">
    <input type="hidden" name="module" value="stats">
    <input type="hidden" name="action" value="index">
    <select name="term" class="form-select form-select-sm w-auto">
        <option value="">All Terms</option>
        <?php foreach ($terms as $t): ?>
            <option value="<?= htmlspecialchars($t) ?>" <?= ($filterTerm ?? '') === $t ? 'selected' : '' ?>><?= htmlspecialchars($t) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline-secondary btn-sm">Filter</button>
</form>
<?php endif; ?>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr><th>#</th><th>Software</th><th>Term</th><th>Allocated</th><th>Activated</th><th>Rate</th></tr>
            </thead>
            <tbody>
                <?php if (empty($stats)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No stats found.</td></tr>
                <?php else: foreach ($stats as $s): ?>
                    <tr>
                        <td><?= $s['id'] ?></td>
                        <td><?= htmlspecialchars($s['software_name']) ?></td>
                        <td><span class="badge bg-info text-dark"><?= htmlspecialchars($s['term_name']) ?></span></td>
                        <td><?= $s['total_allocated'] ?></td>
                        <td><?= $s['total_activated'] ?></td>
                        <td>
                            <div class="progress" style="min-width:80px">
                                <div class="progress-bar <?= $s['activation_rate'] >= 75 ? 'bg-success' : ($s['activation_rate'] >= 40 ? 'bg-warning' : 'bg-danger') ?>"
                                     style="width:<?= $s['activation_rate'] ?>%">
                                    <?= $s['activation_rate'] ?>%
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php require __DIR__ . '/../layout/footer.php'; ?>
