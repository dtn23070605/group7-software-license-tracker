<?php
/**
 * Internal REST API — Update Allocation Status
 *
 * POST /api/update_status.php
 * Body (form-encoded): id=X&status=ACTIVE|EXPIRED|REVOKED
 *
 * Cập nhật trạng thái allocation và trả về JSON,
 * cho phép views/allocation/index.php đổi badge trực tiếp
 * trên bảng mà không cần reload toàn trang.
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/LicenseAllocation.php';

$response = ['success' => false, 'message' => '', 'status' => null];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $response['message'] = 'Method not allowed.';
    echo json_encode($response);
    exit;
}

$id     = (int)($_POST['id'] ?? 0);
$status = trim($_POST['status'] ?? '');

$allowedStatuses = ['ACTIVE', 'EXPIRED', 'REVOKED'];

if ($id <= 0) {
    $response['message'] = 'ID không hợp lệ.';
    echo json_encode($response);
    exit;
}

if (!in_array($status, $allowedStatuses)) {
    $response['message'] = 'Trạng thái không hợp lệ.';
    echo json_encode($response);
    exit;
}

$model      = new LicenseAllocation();
$allocation = $model->getById($id);

if (!$allocation) {
    http_response_code(404);
    $response['message'] = 'Không tìm thấy allocation.';
    echo json_encode($response);
    exit;
}

$updated = $model->updateStatus($id, $status);

if ($updated) {
    $response['success'] = true;
    $response['status']  = $status;
    $response['message'] = 'Cập nhật trạng thái thành công.';
} else {
    $response['message'] = 'Cập nhật thất bại, vui lòng thử lại.';
}

echo json_encode($response);
