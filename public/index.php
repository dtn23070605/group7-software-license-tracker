<?php
require_once __DIR__ . '/../auth/Auth.php';
require_once __DIR__ . '/../controllers/SoftwareTitleController.php';
require_once __DIR__ . '/../controllers/AllocationRuleController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/LicensePoolController.php';
require_once __DIR__ . '/../controllers/LicenseAllocationController.php';
require_once __DIR__ . '/../controllers/ActivationLogController.php';
require_once __DIR__ . '/../controllers/ExpiryNotificationController.php';
require_once __DIR__ . '/../controllers/RevocationLogController.php';
require_once __DIR__ . '/../controllers/UsageStatController.php';

// RBAC: phải đăng nhập mới được vào bất kỳ module nào.
Auth::requireLogin();

$module = $_GET['module'] ?? 'software';
$action = $_GET['action'] ?? 'index';

$allowedModules = ['software', 'rule', 'user', 'pool', 'allocation', 'activation', 'expiry', 'revocation', 'stats'];
$allowedActions = ['index', 'create', 'edit', 'delete'];

// RBAC: các module chỉ Admin (TEACHER) mới được truy cập
$adminOnlyModules = ['software', 'user', 'rule', 'pool', 'activation', 'expiry', 'revocation', 'stats'];

if (!in_array($module, $allowedModules)) $module = 'software';
if (!in_array($action, $allowedActions)) $action = 'index';

if (in_array($module, $adminOnlyModules)) {
    Auth::requireAdmin();
}

switch ($module) {
    case 'rule':       $controller = new AllocationRuleController();      break;
    case 'user':       $controller = new UserController();                break;
    case 'pool':       $controller = new LicensePoolController();         break;
    case 'allocation': $controller = new LicenseAllocationController();   break;
    case 'activation': $controller = new ActivationLogController();       break;
    case 'expiry':     $controller = new ExpiryNotificationController();  break;
    case 'revocation': $controller = new RevocationLogController();       break;
    case 'stats':      $controller = new UsageStatController();           break;
    default:           $controller = new SoftwareTitleController();
}

$controller->$action();
