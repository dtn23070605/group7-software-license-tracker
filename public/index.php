<?php
require_once __DIR__ . '/../controllers/SoftwareTitleController.php';
require_once __DIR__ . '/../controllers/AllocationRuleController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/LicensePoolController.php';
require_once __DIR__ . '/../controllers/LicenseAllocationController.php';
require_once __DIR__ . '/../controllers/ActivationLogController.php';
require_once __DIR__ . '/../controllers/ExpiryNotificationController.php';
require_once __DIR__ . '/../controllers/RevocationLogController.php';
require_once __DIR__ . '/../controllers/UsageStatController.php';

$module = $_GET['module'] ?? 'software';
$action = $_GET['action'] ?? 'index';

$allowedModules = ['software', 'rule', 'user', 'pool', 'allocation', 'activation', 'expiry', 'revocation', 'stats'];
$allowedActions = ['index', 'create', 'edit', 'delete'];

if (!in_array($module, $allowedModules)) $module = 'software';
if (!in_array($action, $allowedActions)) $action = 'index';

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
