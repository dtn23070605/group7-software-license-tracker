<?php
/**
 * Export Usage Stats to Excel
 *
 * GET /export/export_excel.php?term=HK1_2026 (optional filter)
 *
 * Xuất bảng usage_stats ra file .xls bằng cách trả về HTML table
 * với Content-Type đặc biệt mà Excel hiểu được trực tiếp.
 * Không cần composer hay thư viện ngoài — chạy được trên pure XAMPP.
 */

require_once __DIR__ . '/../models/UsageStat.php';

$model      = new UsageStat();
$filterTerm = $_GET['term'] ?? '';

$stats = $filterTerm ? $model->getByTerm($filterTerm) : $model->getAll();

$filename = 'usage_stats_' . ($filterTerm ?: 'all') . '_' . date('Ymd') . '.xls';

// Header báo cho browser đây là file Excel để download, không hiển thị trên trang
header('Content-Type: application/vnd.ms-excel; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');
?>
<table border="1">
    <thead>
        <tr style="background-color:#212529; color:#ffffff; font-weight:bold;">
            <th>ID</th>
            <th>Software</th>
            <th>Term</th>
            <th>Total Allocated</th>
            <th>Total Activated</th>
            <th>Activation Rate (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($stats)): ?>
            <tr><td colspan="6" style="text-align:center">No data found.</td></tr>
        <?php else: foreach ($stats as $s): ?>
            <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['software_name']) ?></td>
                <td><?= htmlspecialchars($s['term_name']) ?></td>
                <td><?= $s['total_allocated'] ?></td>
                <td><?= $s['total_activated'] ?></td>
                <td><?= $s['activation_rate'] ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>
