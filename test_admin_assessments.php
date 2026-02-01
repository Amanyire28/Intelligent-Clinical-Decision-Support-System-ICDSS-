<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set admin session
$_SESSION['user_id'] = 1;
$_SESSION['user_name'] = 'Admin User';
$_SESSION['user_role'] = 'admin';
$_SESSION['user_email'] = 'admin@icdss.local';

echo "Session set up:\n";
echo json_encode($_SESSION, JSON_PRETTY_PRINT);
echo "\n\nNow including admin controller...\n\n";

// Now test the controller
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Assessment.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/RiskResult.php';
require_once __DIR__ . '/controllers/AdminController.php';

$controller = new AdminController();
$controller->viewAssessments();
?>
