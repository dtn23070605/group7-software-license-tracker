<?php require __DIR__ . '/../layout/header.php'; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0">Add Allocation</h4>
    <a href="index.php?module=allocation&action=index" class="btn btn-outline-secondary btn-sm">← Back</a>
</div>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<div class="card shadow-sm" style="max-width:500px">
    <div class="card-body">

        <!-- AJAX preview box: cập nhật real-time khi chọn Pool/User -->
        <div id="ajaxPreview" class="alert alert-info mb-3" style="font-size:0.875rem">
            ℹ️ Chọn Pool<?= Auth::isAdmin() ? ' và User' : '' ?> để xem cảnh báo và ngày hết hạn dự kiến.
        </div>

        <form method="POST" action="index.php?module=allocation&action=create" id="allocationForm">
            <div class="mb-3">
                <label class="form-label">License Pool <span class="text-danger">*</span></label>
                <select name="pool_id" id="poolSelect" class="form-select" required>
                    <option value="">-- Select --</option>
                    <?php foreach ($pools as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= ($_POST['pool_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($p['software_name']) ?> (<?= $p['available_quantity'] ?> available, expires <?= date('d M Y', strtotime($p['expiry_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (Auth::isAdmin()): ?>
                <!-- RBAC: Admin được chọn user bất kỳ -->
                <div class="mb-3">
                    <label class="form-label">User <span class="text-danger">*</span></label>
                    <select name="user_id" id="userSelect" class="form-select" required>
                        <option value="">-- Select --</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u['id'] ?>" <?= ($_POST['user_id'] ?? '') == $u['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u['username']) ?> (<?= $u['role'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">STUDENT: tối đa 180 ngày, tối đa 3 license. TEACHER: tối đa 365 ngày.</div>
                </div>
            <?php else: ?>
                <!-- RBAC: Student tự cấp phát cho chính mình, ẩn dropdown User -->
                <input type="hidden" name="user_id" id="userSelect" value="<?= Auth::getUserId() ?>">
                <div class="alert alert-secondary" style="font-size:0.85rem">
                    Bạn đang yêu cầu license cho chính tài khoản của mình: <strong><?= htmlspecialchars(Auth::getUsername()) ?></strong>
                </div>
                <div class="form-text mb-3">STUDENT: tối đa 180 ngày, tối đa 3 license cùng lúc.</div>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary" id="submitBtn">Save</button>
        </form>
    </div>
</div>

<script>
(function () {
    const poolSelect   = document.getElementById('poolSelect');
    const userSelect   = document.getElementById('userSelect');
    const previewBox   = document.getElementById('ajaxPreview');
    const submitBtn    = document.getElementById('submitBtn');
    const isUserSelectFixed = userSelect.tagName === 'INPUT'; // true nếu Student (hidden input)

    async function checkAllocation() {
        const poolId = poolSelect.value;
        const userId = userSelect.value;

        if (!poolId || !userId) {
            previewBox.className = 'alert alert-info mb-3';
            previewBox.style.fontSize = '0.875rem';
            previewBox.innerHTML = 'ℹ️ Chọn Pool để xem cảnh báo và ngày hết hạn dự kiến.';
            submitBtn.disabled = false;
            return;
        }

        try {
            const res = await fetch(`../api/check_allocation.php?pool_id=${poolId}&user_id=${userId}`);
            const data = await res.json();

            if (data.valid) {
                previewBox.className = 'alert alert-success mb-3';
                previewBox.innerHTML =
                    `✅ Hợp lệ — License sẽ hết hạn vào <strong>${data.preview.valid_until_display}</strong> (role: ${data.preview.role}).`;
                submitBtn.disabled = false;
            } else {
                previewBox.className = 'alert alert-danger mb-3';
                previewBox.innerHTML =
                    '⚠️ ' + data.warnings.join('<br>⚠️ ');
                submitBtn.disabled = true;
            }
        } catch (err) {
            previewBox.className = 'alert alert-warning mb-3';
            previewBox.innerHTML = 'Không thể kiểm tra ngay lúc này, vẫn có thể submit để server validate lại.';
            submitBtn.disabled = false;
        }
    }

    poolSelect.addEventListener('change', checkAllocation);
    if (!isUserSelectFixed) {
        userSelect.addEventListener('change', checkAllocation);
    } else {
        // Student: user_id cố định, chỉ cần check khi đổi pool
        checkAllocation();
    }
})();
</script>

<?php require __DIR__ . '/../layout/footer.php'; ?>
