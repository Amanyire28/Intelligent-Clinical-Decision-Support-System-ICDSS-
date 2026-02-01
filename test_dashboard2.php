<?php
session_start();

// Set admin session
$_SESSION['user_id'] = 2;
$_SESSION['user_name'] = 'System Administrator';
$_SESSION['user_role'] = 'admin';

require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Assessment.php';
require_once __DIR__ . '/models/RiskResult.php';
require_once __DIR__ . '/controllers/AdminController.php';

$controller = new AdminController();

// Capture output
ob_start();
$controller->dashboard();
$output = ob_get_clean();

// Extract the stat card section
preg_match('/<div class="stats-row">.*?<\/div>\s*<\/div>/s', $output, $matches);
if (!empty($matches)) {
    echo "<h2>Stat Cards Output:</h2>";
    echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
} else {
    echo "Could not find stats-row";
}
?>
