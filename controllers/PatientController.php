<?php
/**
 * PATIENT CONTROLLER
 * ============================================================================
 * Handles patient-related operations
 * ============================================================================
 */

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Assessment.php';

class PatientController {
    private $db;
    private $patientModel;
    private $assessmentModel;
    
    public function __construct() {
        $this->db = getDBConnection();
        $this->patientModel = new Patient($this->db);
        $this->assessmentModel = new Assessment($this->db);
    }
    
    /**
     * Search for patients
     */
    public function searchPatients() {
        // Handle AJAX search requests
        if (isset($_GET['action']) && $_GET['action'] === 'search') {
            $search_term = isset($_GET['term']) ? htmlspecialchars($_GET['term']) : '';
            
            if (strlen($search_term) < 2) {
                header('Content-Type: application/json');
                echo json_encode(['results' => []]);
                exit;
            }
            
            $results = $this->patientModel->searchPatients($search_term);
            
            header('Content-Type: application/json');
            echo json_encode(['results' => $results]);
            exit;
        }
        
        // Display patient search/history view
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        // Get recent assessments for the current doctor
        $recent_assessments = $this->getRecentAssessments($_SESSION['user_id']);
        
        // Include patient search view
        include __DIR__ . '/../views/patient_search.php';
    }
    
    /**
     * Get recent assessments for a doctor
     */
    private function getRecentAssessments($doctor_id, $limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    a.id,
                    a.assessment_date,
                    p.first_name,
                    p.last_name,
                    p.medical_record_number,
                    rr.risk_score,
                    rr.risk_level,
                    rr.confidence_percentage
                FROM assessments a
                INNER JOIN patients p ON a.patient_id = p.id
                LEFT JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE a.doctor_id = :doctor_id
                ORDER BY a.assessment_date DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting recent assessments: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get patient assessment history
     */
    public function getPatientHistory($patient_id) {
        return $this->assessmentModel->getPatientAssessments($patient_id);
    }
    
    /**
     * API: Search for patients (AJAX endpoint)
     * POST: search_term (patient name or MRN)
     */
    public function searchPatientAPI() {
        header('Content-Type: application/json');
        
        error_log("=== API SEARCH START ===");
        error_log("Request method: " . $_SERVER['REQUEST_METHOD']);
        error_log("POST data: " . print_r($_POST, true));
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            error_log("Invalid request method");
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $search_term = isset($_POST['search_term']) ? trim($_POST['search_term']) : '';
        error_log("Search term after trim: '$search_term' (length: " . strlen($search_term) . ")");
        
        if (strlen($search_term) < 2) {
            error_log("Search term too short, returning empty");
            echo json_encode(['success' => false, 'patients' => [], 'message' => 'Search term must be at least 2 characters']);
            exit;
        }
        
        try {
            error_log("Calling patientModel->searchPatients()");
            $results = $this->patientModel->searchPatients($search_term);
            error_log("API: Found " . count($results) . " patients");
            
            // Enhance results with last assessment date
            if (!empty($results)) {
                foreach ($results as $key => $patient) {
                    error_log("Fetching last assessment for patient " . $patient['id']);
                    $lastAssessment = $this->assessmentModel->getLastPatientAssessment($patient['id']);
                    if ($lastAssessment) {
                        $results[$key]['last_assessment_date'] = $lastAssessment['assessment_date'];
                        error_log("  Last assessment: " . $lastAssessment['assessment_date']);
                    } else {
                        $results[$key]['last_assessment_date'] = null;
                        error_log("  No previous assessment");
                    }
                }
            }
            
            error_log("=== API SEARCH END - SUCCESS ===");
            echo json_encode(['success' => true, 'patients' => $results]);
        } catch (Exception $e) {
            error_log("Patient search error: " . $e->getMessage());
            error_log("=== API SEARCH END - ERROR ===");
            echo json_encode(['success' => false, 'message' => 'Search failed: ' . $e->getMessage(), 'patients' => []]);
        }
        exit;
    }
}
?>
