<?php
// Test the API endpoint directly
session_start();
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Patient.php';
require_once __DIR__ . '/models/Assessment.php';

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;  // For testing
    $_SESSION['user_role'] = 'doctor';
}

// Simulate POST request
$_POST['search_term'] = 'a';
$_SERVER['REQUEST_METHOD'] = 'POST';

header('Content-Type: application/json');

$search_term = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
echo "<!-- Debug: Search term = '$search_term' (length: " . strlen($search_term) . ") -->\n";

if (strlen($search_term) < 2) {
    echo json_encode(['success' => false, 'patients' => [], 'message' => 'Search term too short']);
    exit;
}

try {
    $db = getDBConnection();
    $patientModel = new Patient($db);
    $assessmentModel = new Assessment($db);
    
    $results = $patientModel->searchPatients($search_term);
    echo "<!-- Debug: Found " . count($results) . " patients -->\n";
    
    if (!empty($results)) {
        foreach ($results as $key => $patient) {
            $lastAssessment = $assessmentModel->getLastPatientAssessment($patient['id']);
            if ($lastAssessment) {
                $results[$key]['last_assessment_date'] = $lastAssessment['assessment_date'];
            } else {
                $results[$key]['last_assessment_date'] = null;
            }
        }
    }
    
    echo json_encode(['success' => true, 'patients' => $results]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage(), 'patients' => []]);
}
?>
