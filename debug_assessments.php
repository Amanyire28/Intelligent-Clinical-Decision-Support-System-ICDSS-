<?php
/**
 * DEBUG: Test getAllAssessments query
 */

require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Assessment.php';

// Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $db = getDBConnection();
    $assessmentModel = new Assessment($db);
    
    // Test the method
    $assessments = $assessmentModel->getAllAssessments(50, 0);
    
    echo "<pre>";
    echo "Total assessments returned: " . count($assessments) . "\n\n";
    echo "Data:\n";
    print_r($assessments);
    echo "</pre>";
    
    // Also test raw query
    echo "<hr><h3>Direct Query Test:</h3>";
    $stmt = $db->prepare("
        SELECT a.id as assessment_id, a.assessment_date, 
               p.first_name, p.last_name,
               u.full_name as doctor_name,
               rr.risk_level, rr.risk_score, rr.confidence_percentage
        FROM assessments a
        JOIN patients p ON a.patient_id = p.id
        JOIN users u ON a.doctor_id = u.id
        LEFT JOIN risk_results rr ON a.id = rr.assessment_id
        ORDER BY a.assessment_date DESC
        LIMIT 50 OFFSET 0
    ");
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo "<pre>";
    echo "Direct query returned: " . count($results) . " records\n\n";
    print_r($results);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
    echo "\n\nStack Trace:\n";
    echo $e->getTraceAsString();
}
?>
