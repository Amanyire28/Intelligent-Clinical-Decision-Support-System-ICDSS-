<?php
/**
 * ASSESSMENT FORM HANDLER
 * Processes patient assessment form submissions
 */

session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}

// Load required files
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Assessment.php';
require_once __DIR__ . '/models/Patient.php';
require_once __DIR__ . '/models/RiskResult.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDBConnection();
        $patientModel = new Patient($db);
        $assessmentModel = new Assessment($db);
        $riskResultModel = new RiskResult($db);
        
        // Get or create patient
        $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : null;
        
        if (!$patient_id) {
            // Create new patient
            $first_name = htmlspecialchars($_POST['first_name'] ?? '');
            $last_name = htmlspecialchars($_POST['last_name'] ?? '');
            $dob = $_POST['date_of_birth'] ?? '';
            $gender = $_POST['gender'] ?? 'Other';
            
            if (empty($first_name) || empty($last_name) || empty($dob)) {
                header('Location: index.php?page=doctor-dashboard&error=missing_patient_info');
                exit;
            }
            
            $patient_id = $patientModel->createPatient($first_name, $last_name, $dob, $gender);
            if (!$patient_id) {
                header('Location: index.php?page=doctor-dashboard&error=patient_creation_failed');
                exit;
            }
        }
        
        // Prepare assessment data
        $assessmentData = [
            'smoking_status' => $_POST['smoking_status'] ?? 'never',
            'smoking_years' => intval($_POST['smoking_years'] ?? 0),
            'alcohol_consumption' => $_POST['alcohol_consumption'] ?? 'none',
            'sore_throat_duration' => intval($_POST['sore_throat_duration'] ?? 0),
            'voice_changes' => isset($_POST['voice_changes']) ? 1 : 0,
            'difficulty_swallowing' => isset($_POST['difficulty_swallowing']) ? 1 : 0,
            'neck_lump' => isset($_POST['neck_lump']) ? 1 : 0,
            'weight_loss' => isset($_POST['weight_loss']) ? 1 : 0,
            'weight_loss_percentage' => floatval($_POST['weight_loss_percentage'] ?? 0),
            'unexplained_weight_loss_weeks' => intval($_POST['weight_loss_weeks'] ?? 0),
            'hpv_status' => $_POST['hpv_status'] ?? 'unknown',
            'family_cancer_history' => isset($_POST['family_cancer_history']) ? 1 : 0,
            'family_cancer_type' => $_POST['family_cancer_type'] ?? null,
            'hemoglobin_level' => floatval($_POST['hemoglobin_level'] ?? 0) ?: null,
            'lymphocyte_count' => floatval($_POST['lymphocyte_count'] ?? 0) ?: null,
            'clinical_notes' => $_POST['clinical_notes'] ?? null
        ];
        
        // Create assessment
        $assessment_id = $assessmentModel->createAssessment($patient_id, $_SESSION['user_id'], $assessmentData);
        
        if ($assessment_id) {
            // Create placeholder risk result (Phase 4 will replace with actual risk engine)
            $riskResultModel->createRiskResult(
                $assessment_id,
                65,  // Placeholder risk score (0-100)
                'moderate',  // Placeholder risk level
                75,  // Placeholder confidence
                'Sore throat symptoms present',  // Primary factors
                'Family history noted',  // Secondary factors
                'Further clinical evaluation recommended'  // Recommendation
            );
            
            // Log the assessment creation
            logSystemAction($_SESSION['user_id'], 'Assessment Created', 'Assessment', $assessment_id, 'New patient assessment created');
            
            // Redirect to results page
            header('Location: index.php?page=assessment-results&id=' . $assessment_id);
            exit;
        } else {
            header('Location: index.php?page=doctor-dashboard&error=assessment_creation_failed');
            exit;
        }
    } catch (Exception $e) {
        error_log("Assessment Handler Error: " . $e->getMessage());
        header('Location: index.php?page=doctor-dashboard&error=server_error');
        exit;
    }
} else {
    header('Location: index.php?page=doctor-dashboard');
    exit;
}
?>
