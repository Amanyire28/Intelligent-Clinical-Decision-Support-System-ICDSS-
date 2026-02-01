<?php
/**
 * ACTION CONTROLLER
 * ============================================================================
 * Handles clinical actions after risk assessment
 * ============================================================================
 */

session_start();

require_once __DIR__ . '/../config/db_config.php';

class ActionController {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    /**
     * Record clinical action (referral, monitoring, etc.)
     */
    public function recordAction() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }
        
        $assessment_id = isset($_POST['assessment_id']) ? intval($_POST['assessment_id']) : 0;
        $action_type = isset($_POST['action_type']) ? htmlspecialchars($_POST['action_type']) : '';
        $action_notes = isset($_POST['action_notes']) ? htmlspecialchars($_POST['action_notes']) : '';
        $follow_up_date = isset($_POST['follow_up_date']) ? $_POST['follow_up_date'] : null;
        
        if (!$assessment_id || !$action_type) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO action_history (assessment_id, doctor_id, action_type, action_notes, follow_up_date)
                VALUES (:assessment_id, :doctor_id, :action_type, :action_notes, :follow_up_date)
            ");
            
            $result = $stmt->execute([
                ':assessment_id' => $assessment_id,
                ':doctor_id' => $_SESSION['user_id'],
                ':action_type' => $action_type,
                ':action_notes' => $action_notes,
                ':follow_up_date' => $follow_up_date
            ]);
            
            if ($result) {
                // Log the action
                logSystemAction($_SESSION['user_id'], 'Clinical Action Recorded', 'Assessment', $assessment_id, "Action: $action_type");
                
                // Redirect back to dashboard
                header('Location: /CANCER/index.php?page=doctor-dashboard');
                exit;
            }
        } catch (PDOException $e) {
            error_log("Error recording action: " . $e->getMessage());
            return false;
        }
        
        return false;
    }
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ActionController();
    $controller->recordAction();
}
?>
