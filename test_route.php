<?php
session_start();

// Set up a test session like a doctor would have
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'doctor';
    $_SESSION['user_name'] = 'Test Doctor';
}

error_log("=== DIRECT ROUTE TEST ===");
error_log("Session setup complete");

// Set GET parameter to simulate calling through index.php
$_GET['page'] = 'api-patient-assessments';
$_POST['patient_id'] = 3;
$_SERVER['REQUEST_METHOD'] = 'POST';

error_log("About to include index.php");
include __DIR__ . '/index.php';
?>
