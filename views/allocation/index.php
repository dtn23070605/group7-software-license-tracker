<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">
        <?= Auth::isAdmin() ? 'License Allocations' : 'My Licenses' ?>
        <span class="badge bg-secondary fs-6"><?= count($allocations) ?></span>
    </h4>
    <a href="index.php?module=allocation&action=create" class="btn btn-primary btn-sm">+ Add Allocation</a>
</div>
<div id="ajaxAlert"></div>
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-dark">
                <tr><th>#</th><th>User</th><th>Software</th><th>Valid Until</th><th>Status</th><?php if (Auth::isAdmin()): ?><th class="text-end">Actions</th><?php endif; ?></tr>
            </thead>
            <tbody>
                <?php if (empty($allocations)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">No allocations found.</td></tr>
                <?php else: foreach ($allocations as $a):
                    $badgeClass = match($a['status']) { 'ACTIVE' => 'bg-success', 'EXPIRED' => 'bg-secondary', 'REVOKED' => 'bg-danger', default => 'bg-secondary' };
                ?>
                    <tr data-allocation-id="<?= $a['id'] ?>">
                        <td><?= $a['id'] ?></td>
                        <td><?= htmlspecialchars($a['username']) ?></td>
                        <td><?= htmlspecialchars($a['software_name']) ?></td>
                        <td><?= date('d M Y', strtotime($a['valid_until'])) ?></td>
                        <td>
                            <?php if (Auth::isAdmin()): ?>
                                <!-- RBAC: chỉ Admin đổi được status -->
                                <select class="form-select form-select-sm status-select <?= $badgeClass ?> text-white" style="width:auto;display:inline-block" data-id="<?= $a['id'] ?>">
                                    <option value="ACTIVE"  <?= $a['status'] === 'ACTIVE'  ? 'selected' : '' ?>>ACTIVE</option>
                                    <option value="EXPIRED" <?= $a['status'] === 'EXPIRED' ? 'selected' : '' ?>>EXPIRED</option>
                                    <option value="REVOKED" <?= $a['status'] === 'REVOKED' ? 'selected' : '' ?>>REVOKED</option>
                                </select>
                            <?php else: ?>
                                <!-- RBAC: Student chỉ xem, không sửa được status -->
                                <span class="badge <?= $badgeClass ?>"><?= $a['status'] ?></span>
                            <?php endif; ?>
                        </td>
                        <?php if (Auth::isAdmin()): ?>
                            <td class="text-end">
                                <a href="index.php?module=allocation&action=delete&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this allocation?')">Delete</a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (Auth::isAdmin()): ?>
<script>
// AJAX: đổi status không reload trang — chỉ Admin có quyền này
document.querySelectorAll('.status-select').forEach(function (select) {
    const badgeClassMap = { ACTIVE: 'bg-success', EXPIRED: 'bg-secondary', REVOKED: 'bg-danger' };

    select.addEventListener('change', async function () {
        const id     = this.dataset.id;
        const status = this.value;
        const alertBox = document.getElementById('ajaxAlert');

        try {
            const res = await fetch('../api/update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
            });
            const data = await res.json();

            if (data.success) {
                Object.values(badgeClassMap).forEach(c => this.classList.remove(c));
                this.classList.add(badgeClassMap[status]);
                alertBox.innerHTML =
                    `<div class="alert alert-success alert-dismissible fade show">Cập nhật trạng thái allocation #${id} thành ${status}.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            } else {
                alertBox.innerHTML =
                    `<div class="alert alert-danger alert-dismissible fade show">${data.message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
            }
        } catch (err) {
            alertBox.innerHTML =
                `<div class="alert alert-warning alert-dismissible fade show">Không thể kết nối server, vui lòng thử lại.<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`;
        }
    });
});
</script>
<?php endif; ?>

<?php require __DIR__ . '/../layout/footer.php'; ?>
