<?php
/**
 * DOCTOR CONTROLLER
 * ============================================================================
 * Handles doctor dashboard and assessment operations
 * ============================================================================
 */

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../models/Assessment.php';
require_once __DIR__ . '/../models/RiskResult.php';

class DoctorController {
    private $db;
    private $assessmentModel;
    private $riskResultModel;
    
    public function __construct() {
        $this->db = getDBConnection();
        $this->assessmentModel = new Assessment($this->db);
        $this->riskResultModel = new RiskResult($this->db);
    }
    
    /**
     * Display doctor dashboard
     */
    public function dashboard() {
        // Get current user info
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        // TODO: Phase 3 - Fetch statistics from database
        // For now, using placeholder values
        $total_assessments = $this->getTotalAssessmentCount();
        $high_risk_count = $this->getHighRiskCount();
        $total_patients = $this->getTotalPatientCount();
        $month_assessments = $this->getMonthAssessmentCount();
        
        // Get recent assessments for current doctor
        $recent_assessments = $this->getRecentAssessments($_SESSION['user_id']);
        
        // Include the dashboard view
        include __DIR__ . '/../views/doctor_dashboard.php';
    }
    
    /**
     * Get total assessment count
     */
    private function getTotalAssessmentCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM assessments WHERE doctor_id = " . intval($_SESSION['user_id']));
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting assessment count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get high-risk assessment count
     */
    private function getHighRiskCount() {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM risk_results rr
                JOIN assessments a ON rr.assessment_id = a.id
                WHERE rr.risk_level = 'High' AND a.doctor_id = :doctor_id
            ");
            $stmt->execute([':doctor_id' => $_SESSION['user_id']]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting high-risk count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total patient count
     */
    private function getTotalPatientCount() {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT patient_id) as count FROM assessments WHERE doctor_id = :doctor_id
            ");
            $stmt->execute([':doctor_id' => $_SESSION['user_id']]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting patient count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get current month assessment count
     */
    private function getMonthAssessmentCount() {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count FROM assessments 
                WHERE doctor_id = :doctor_id 
                AND MONTH(assessment_date) = MONTH(CURDATE())
                AND YEAR(assessment_date) = YEAR(CURDATE())
            ");
            $stmt->execute([':doctor_id' => $_SESSION['user_id']]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting month assessment count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent assessments
     */
    private function getRecentAssessments($doctor_id, $limit = 10) {
        return $this->assessmentModel->getDoctorRecentAssessments($doctor_id, $limit);
    }
}
?>
