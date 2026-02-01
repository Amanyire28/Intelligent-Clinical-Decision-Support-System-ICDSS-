<?php
/**
 * Simulate EXACTLY what happens in index.php for admin-assessments route
 */

// Start session exactly like index.php
session_start();

// Set admin session
$_SESSION['user_id'] = 2;
$_SESSION['username'] = 'admin1';
$_SESSION['user_name'] = 'System Administrator';
$_SESSION['user_role'] = 'admin';
$_SESSION['user_email'] = 'admin1@icdss.local';

// Simulate $_GET as if user visited index.php?page=admin-assessments
$_GET['page'] = 'admin-assessments';

require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/controllers/AdminController.php';

echo "<h2>Simulating Router Execution</h2>";
echo "<p>GET['page'] = " . $_GET['page'] . "</p>";
echo "<p>Session user_role = " . $_SESSION['user_role'] . "</p>";

// This is the exact code from index.php for the admin-assessments case
if ($_SESSION['user_role'] !== 'admin') {
    echo "<p style='color: red;'>AUTH CHECK FAILED - Not admin!</p>";
    exit;
} else {
    echo "<p style='color: green;'>AUTH CHECK PASSED - Is admin</p>";
}

echo "<p>Creating AdminController and calling viewAssessments()...</p>";

$controller = new AdminController();

// Add debugging before and after
echo "<p>BEFORE viewAssessments call</p>";
ob_start();
$controller->viewAssessments();
$output = ob_get_clean();
echo "<p>AFTER viewAssessments call</p>";

// Check what we got
if (strpos($output, 'No assessments') !== false) {
    echo "<p style='color: red;'><strong>OUTPUT CONTAINS: 'No assessments found'</strong></p>";
} else if (strpos($output, 'angel CASTRAL') !== false) {
    echo "<p style='color: green;'><strong>OUTPUT CONTAINS: Assessment data!</strong></p>";
}

// Show debug comment
if (preg_match('/<!-- DEBUG:.*?-->/s', $output, $matches)) {
    echo "<p>Debug comment: " . htmlspecialchars($matches[0]) . "</p>";
}

echo $output;
