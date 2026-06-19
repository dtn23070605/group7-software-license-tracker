<?php
/**
 * Export Usage Stats to PDF
 *
 * GET /export/export_pdf.php?term=HK1_2026 (optional filter)
 *
 * Render một trang HTML thuần (không sidebar) được format đẹp cho in ấn.
 * Người dùng nhấn Ctrl+P hoặc nút "Save as PDF" trên trang để xuất PDF
 * qua chức năng "Print to PDF" có sẵn của mọi browser.
 * Cách này không cần composer hay thư viện PDF ngoài — chạy được trên pure XAMPP.
 */

require_once __DIR__ . '/../models/UsageStat.php';

$model      = new UsageStat();
$filterTerm = $_GET['term'] ?? '';

$stats = $filterTerm ? $model->getByTerm($filterTerm) : $model->getAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Usage Stats Report</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #212529; }
        h1 { font-size: 1.4rem; margin-bottom: 4px; }
        .subtitle { color: #6c757d; margin-bottom: 24px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #dee2e6; padding: 8px 12px; text-align: left; font-size: 0.9rem; }
        th { background-color: #212529; color: #fff; }
        tr:nth-child(even) { background-color: #f8f9fa; }
        .rate-high { color: #198754; font-weight: bold; }
        .rate-mid  { color: #b08400; font-weight: bold; }
        .rate-low  { color: #dc3545; font-weight: bold; }
        .print-btn {
            margin-bottom: 20px; padding: 8px 16px; background: #0d6efd; color: #fff;
            border: none; border-radius: 4px; cursor: pointer; font-size: 0.9rem;
        }
        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">🖨️ Save as PDF / Print</button>

    <h1>Software License Tracker — Usage Stats Report</h1>
    <div class="subtitle">
        Term: <?= htmlspecialchars($filterTerm ?: 'All Terms') ?> &nbsp;|&nbsp;
        Generated: <?= date('d M Y, H:i') ?>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Software</th>
                <th>Term</th>
                <th>Total Allocated</th>
                <th>Total Activated</th>
                <th>Activation Rate</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($stats)): ?>
                <tr><td colspan="6" style="text-align:center">No data found.</td></tr>
            <?php else: foreach ($stats as $s):
                $rate = (float)$s['activation_rate'];
                $rateClass = $rate >= 75 ? 'rate-high' : ($rate >= 40 ? 'rate-mid' : 'rate-low');
            ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['software_name']) ?></td>
                    <td><?= htmlspecialchars($s['term_name']) ?></td>
                    <td><?= $s['total_allocated'] ?></td>
                    <td><?= $s['total_activated'] ?></td>
                    <td class="<?= $rateClass ?>"><?= $rate ?>%</td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</body>
</html>
