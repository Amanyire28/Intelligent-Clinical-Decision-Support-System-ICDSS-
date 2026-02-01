<?php
// Direct test without going through index.php
session_start();

// Set up test session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'doctor';
    $_SESSION['user_name'] = 'Test Doctor';
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

error_log("=== TEST: About to require config/db_config.php ===");
require_once __DIR__ . '/config/db_config.php';
error_log("=== TEST: Got db_config ===");

error_log("=== TEST: About to require controllers/AssessmentController.php ===");
require_once __DIR__ . '/controllers/AssessmentController.php';
error_log("=== TEST: Got AssessmentController ===");

error_log("=== TEST: About to create AssessmentController instance ===");
$controller = new AssessmentController();
error_log("=== TEST: Created instance ===");

error_log("=== TEST: About to set POST data ===");
$_POST['patient_id'] = 3;
$_SERVER['REQUEST_METHOD'] = 'POST';

error_log("=== TEST: About to call getPatientAssessmentsAPI ===");
header('Content-Type: application/json');
$controller->getPatientAssessmentsAPI();
error_log("=== TEST: Done ===");
?>
