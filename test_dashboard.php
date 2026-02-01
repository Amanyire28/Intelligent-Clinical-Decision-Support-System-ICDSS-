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

// Check what variables are being set
echo "<h2>Dashboard Test</h2>";
echo "<p>View file included. Checking for statistics...</p>";

// Look for the stat values in the output
if (preg_match('/Total Assessments<\/p>.*?<p class="stat-value">(\d+)<\/p>/s', $output, $matches)) {
    echo "<p>Total Assessments: " . $matches[1] . "</p>";
} else {
    echo "<p style='color: red;'>Total Assessments: NOT FOUND IN OUTPUT</p>";
}

if (preg_match('/Active Doctors<\/p>.*?<p class="stat-value">(\d+)<\/p>/s', $output, $matches)) {
    echo "<p>Active Doctors: " . $matches[1] . "</p>";
}

if (preg_match('/Registered Patients<\/p>.*?<p class="stat-value">(\d+)<\/p>/s', $output, $matches)) {
    echo "<p>Registered Patients: " . $matches[1] . "</p>";
}

if (preg_match('/High-Risk Cases<\/p>.*?<p class="stat-value">(\d+)<\/p>/s', $output, $matches)) {
    echo "<p>High-Risk Cases: " . $matches[1] . "</p>";
}
?>
