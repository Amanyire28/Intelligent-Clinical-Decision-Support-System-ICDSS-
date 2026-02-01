<?php
/**
 * ASSESSMENT CONTROLLER
 * ============================================================================
 * Handles patient assessment creation and result display
 * ============================================================================
 */

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../models/Assessment.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/RiskResult.php';

class AssessmentController {
    private $db;
    private $assessmentModel;
    private $patientModel;
    private $riskResultModel;
    
    public function __construct() {
        $this->db = getDBConnection();
        $this->assessmentModel = new Assessment($this->db);
        $this->patientModel = new Patient($this->db);
        $this->riskResultModel = new RiskResult($this->db);
    }
    
    /**
     * Create new assessment
     */
    public function createAssessment() {
        // Handle GET requests - display the assessment form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $current_user = [
                'id' => $_SESSION['user_id'],
                'full_name' => $_SESSION['user_name']
            ];
            include __DIR__ . '/../views/patient_assessment.php';
            return;
        }
        
        // Handle POST requests - process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect assessment data
            $patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : null;
            $action = isset($_POST['action']) ? $_POST['action'] : '';
            
            if (!$patient_id) {
                // Create new patient if needed
                $first_name = isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '';
                $last_name = isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '';
                $dob = isset($_POST['date_of_birth']) ? $_POST['date_of_birth'] : '';
                $gender = isset($_POST['gender']) ? $_POST['gender'] : '';
                
                $patient_id = $this->patientModel->createPatient($first_name, $last_name, $dob, $gender);
                
                if (!$patient_id) {
                    $this->sendError('Failed to create patient record');
                    return;
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
            
            // Create assessment record
            $assessment_id = $this->assessmentModel->createAssessment($patient_id, $_SESSION['user_id'], $assessmentData);
            
            if (!$assessment_id) {
                $this->sendError('Failed to create assessment');
                return;
            }
            
            // TODO: Phase 4 - Call RiskEngine to calculate risk score
            // For now, create placeholder result
            $this->riskResultModel->createPlaceholderResult($assessment_id);
            
            // Log the action
            logSystemAction($_SESSION['user_id'], 'Assessment Created', 'Assessment', $assessment_id, "New assessment for patient $patient_id");
            
            // Redirect to results page
            header('Location: /CANCER/index.php?page=assessment-results&id=' . $assessment_id);
            exit;
        }
    }
    
    /**
     * Display assessment results
     */
    public function displayResults($assessment_id) {
        // Get assessment details
        $assessment = $this->assessmentModel->getAssessmentById($assessment_id);
        
        if (!$assessment) {
            $this->sendError('Assessment not found');
            return;
        }
        
        // Get risk result
        $risk_result = $this->riskResultModel->getRiskResultByAssessmentId($assessment_id);
        
        if (!$risk_result) {
            $this->sendError('Risk result not found');
            return;
        }
        
        // Get current user
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        // Include assessment results view
        include __DIR__ . '/../views/assessment_results.php';
    }
    
    /**
     * Send error response
     */
    private function sendError($message) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode(['error' => $message]);
        exit;
    }
    
    /**
     * API: Get patient assessments (AJAX endpoint)
     * POST: patient_id
     */
    public function getPatientAssessmentsAPI() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $patient_id = intval($_POST['patient_id'] ?? 0);
        
        if ($patient_id <= 0) {
            echo json_encode(['success' => false, 'assessments' => []]);
            exit;
        }
        
        try {
            $assessments = $this->assessmentModel->getPatientAssessments($patient_id);
            
            // Enhance with outcome status
            if (!empty($assessments)) {
                foreach ($assessments as &$assessment) {
                    // Check if outcome is recorded
                    $outcome = $this->getAssessmentOutcome($assessment['id']);
                    $assessment['outcome_status'] = $outcome ? 'recorded' : 'pending';
                }
            }
            
            echo json_encode(['success' => true, 'assessments' => $assessments]);
        } catch (Exception $e) {
            error_log("Get patient assessments error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load assessments', 'assessments' => []]);
        }
        exit;
    }
    
    /**
     * Check if assessment has a recorded outcome
     */
    private function getAssessmentOutcome($assessment_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id FROM patient_outcomes 
                WHERE assessment_id = :assessment_id 
                LIMIT 1
            ");
            $stmt->bindValue(':assessment_id', $assessment_id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch();
        } catch (Exception $e) {
            error_log("Error checking assessment outcome: " . $e->getMessage());
            return null;
        }
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AssessmentController();
    $controller->createAssessment();
}
?>
