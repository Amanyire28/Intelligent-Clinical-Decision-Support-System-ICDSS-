<?php
// Direct test of the assessments API
session_start();

// Set up a test session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'doctor';
    $_SESSION['user_name'] = 'Test Doctor';
}

echo "Session user_id: " . $_SESSION['user_id'] . "\n";
echo "Session user_role: " . $_SESSION['user_role'] . "\n";

// Now make the request to the API endpoint
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Assessment.php';
require_once __DIR__ . '/models/Patient.php';

$_POST['patient_id'] = 3;
$_SERVER['REQUEST_METHOD'] = 'POST';

header('Content-Type: application/json');

error_log("TEST: Starting direct API call");
error_log("TEST: POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    error_log("TEST: Invalid request method");
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$patient_id = intval($_POST['patient_id'] ?? 0);
error_log("TEST: Patient ID = $patient_id");

if ($patient_id <= 0) {
    error_log("TEST: Invalid patient ID");
    echo json_encode(['success' => false, 'assessments' => []]);
    exit;
}

try {
    error_log("TEST: Getting DB connection");
    $db = getDBConnection();
    error_log("TEST: Got DB connection");
    
    $assessmentModel = new Assessment($db);
    error_log("TEST: Created assessment model");
    
    $assessments = $assessmentModel->getPatientAssessments($patient_id);
    error_log("TEST: Found " . count($assessments) . " assessments");
    
    error_log("TEST: Assessments: " . print_r($assessments, true));
    
    echo json_encode(['success' => true, 'assessments' => $assessments]);
} catch (Exception $e) {
    error_log("TEST: Error - " . $e->getMessage());
    error_log("TEST: Stack trace: " . $e->getTraceAsString());
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'assessments' => []]);
}
exit;
?>
