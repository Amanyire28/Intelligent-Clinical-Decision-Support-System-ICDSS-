<?php
/**
 * ADMIN CONTROLLER
 * ============================================================================
 * Handles admin dashboard and system management
 * ============================================================================
 */

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Assessment.php';
require_once __DIR__ . '/../models/RiskResult.php';

class AdminController {
    private $db;
    private $userModel;
    private $assessmentModel;
    private $riskResultModel;
    
    public function __construct() {
        $this->db = getDBConnection();
        $this->userModel = new User($this->db);
        $this->assessmentModel = new Assessment($this->db);
        $this->riskResultModel = new RiskResult($this->db);
    }
    
    /**
     * Display admin dashboard
     */
    public function dashboard() {
        // Get current user
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        // Get system statistics
        $total_assessments = $this->getTotalAssessmentCount();
        $active_doctors = $this->getActiveDoctorCount();
        $total_patients = $this->getTotalPatientCount();
        $high_risk_cases = $this->getHighRiskCaseCount();
        
        // Get risk statistics
        $risk_statistics = $this->riskResultModel->getRiskStatistics();
        
        // Get high-risk patients
        $high_risk_patients = $this->riskResultModel->getHighRiskPatients(20);
        
        // Get all doctors
        $doctors = $this->userModel->getAllDoctors();
        
        // Add assessment count to each doctor
        foreach ($doctors as &$doctor) {
            $doctor['assessment_count'] = $this->getDoctorAssessmentCount($doctor['id']);
        }
        
        // Get recent logs
        $recent_logs = $this->getRecentLogs(10);
        
        // Include admin dashboard view
        include __DIR__ . '/../views/admin_dashboard.php';
    }
    
    /**
     * Manage users page
     */
    public function manageUsers() {
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        $doctors = $this->userModel->getAllDoctors();
        $admins = $this->userModel->getAllAdmins();
        
        // Add assessment count to each doctor
        foreach ($doctors as &$doctor) {
            $doctor['assessment_count'] = $this->getDoctorAssessmentCount($doctor['id']);
        }
        
        // Include user management view
        include __DIR__ . '/../views/admin_users.php';
    }
    
    /**
     * View all assessments
     */
    public function viewAssessments() {
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        // Get pagination page number from 'pag' parameter (not 'page' which is the route)
        $pageNum = isset($_GET['pag']) ? intval($_GET['pag']) : 1;
        $limit = 50;
        $offset = ($pageNum - 1) * $limit;
        
        $assessments = $this->assessmentModel->getAllAssessments($limit, $offset);
        $total_count = $this->assessmentModel->getTotalAssessmentCount();
        $total_pages = ceil($total_count / $limit);
        
        // Get statistics for the view
        $stats_total_assessments = $total_count;
        $stats_this_month = $this->getAssessmentsThisMonth();
        $stats_high_risk = $this->getHighRiskAssessments();
        $stats_low_risk = $this->getLowRiskAssessments();
        
        // Include assessments view
        include __DIR__ . '/../views/admin_assessments.php';
    }
    
    /**
     * Get assessments created this month
     */
    private function getAssessmentsThisMonth() {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM assessments 
                WHERE MONTH(assessment_date) = MONTH(CURDATE()) 
                AND YEAR(assessment_date) = YEAR(CURDATE())
            ");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting assessments this month: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get high-risk assessments
     */
    private function getHighRiskAssessments() {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM risk_results WHERE risk_level = 'High'
            ");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting high-risk assessments: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get low-risk assessments
     */
    private function getLowRiskAssessments() {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM risk_results WHERE risk_level = 'Low'
            ");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting low-risk assessments: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total assessment count
     */
    private function getTotalAssessmentCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM assessments");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting assessment count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get active doctor count
     */
    private function getActiveDoctorCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM users WHERE role = 'doctor' AND is_active = TRUE");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting doctor count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get total patient count
     */
    private function getTotalPatientCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM patients");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting patient count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get high-risk case count
     */
    private function getHighRiskCaseCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM risk_results WHERE risk_level = 'High'");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting high-risk count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get assessment count for specific doctor
     */
    private function getDoctorAssessmentCount($doctor_id) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM assessments WHERE doctor_id = :doctor_id");
            $stmt->execute([':doctor_id' => $doctor_id]);
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (Exception $e) {
            error_log("Error getting doctor assessment count: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get recent system logs
     */
    private function getRecentLogs($limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT l.*, u.full_name as user_name
                FROM system_logs l
                LEFT JOIN users u ON l.user_id = u.id
                ORDER BY l.created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error getting logs: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * View reports
     */
    public function viewReports() {
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        // Get system statistics for reports
        $total_assessments = $this->getTotalAssessmentCount();
        $active_doctors = $this->getActiveDoctorCount();
        $total_patients = $this->getTotalPatientCount();
        $high_risk_cases = $this->getHighRiskCaseCount();
        
        // Placeholder view
        echo "<div class='container'><h2>System Reports (Coming Soon)</h2></div>";
    }
    
    /**
     * Configuration page
     */
    public function config() {
        $current_user = [
            'id' => $_SESSION['user_id'],
            'full_name' => $_SESSION['user_name']
        ];
        
        // Placeholder view
        echo "<div class='container'><h2>System Configuration (Coming Soon)</h2></div>";
    }

    /**
     * Handle AJAX requests for user operations
     */
    public function handleUserAction() {
        header('Content-Type: application/json');

        // Get action from request
        $action = isset($_POST['action']) ? $_POST['action'] : null;

        switch ($action) {
            case 'add':
                echo json_encode($this->addUser());
                break;

            case 'edit':
                echo json_encode($this->editUser());
                break;

            case 'delete':
                echo json_encode($this->deleteUser());
                break;

            case 'toggle-status':
                echo json_encode($this->toggleUserStatus());
                break;

            case 'get-user':
                echo json_encode($this->getUser());
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
        exit;
    }

    /**
     * Add new user (doctor or admin)
     */
    private function addUser() {
        try {
            $username = isset($_POST['username']) ? sanitizeInput($_POST['username']) : null;
            $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : null;
            $password = isset($_POST['password']) ? $_POST['password'] : null;
            $full_name = isset($_POST['full_name']) ? sanitizeInput($_POST['full_name']) : null;
            $role = isset($_POST['role']) ? sanitizeInput($_POST['role']) : 'doctor';
            $specialization = isset($_POST['specialization']) ? sanitizeInput($_POST['specialization']) : null;
            $license_number = isset($_POST['license_number']) ? sanitizeInput($_POST['license_number']) : null;

            // Validation
            if (!$username || !$email || !$password || !$full_name) {
                return ['success' => false, 'message' => 'All required fields must be filled'];
            }

            if (strlen($password) < 6) {
                return ['success' => false, 'message' => 'Password must be at least 6 characters'];
            }

            if ($this->userModel->usernameExists($username)) {
                return ['success' => false, 'message' => 'Username already exists'];
            }

            if ($this->userModel->emailExists($email)) {
                return ['success' => false, 'message' => 'Email already in use'];
            }

            // Create user
            $user_id = $this->userModel->createUser($username, $email, $password, $full_name, $role, $specialization, $license_number);

            if ($user_id) {
                logSystemAction($_SESSION['user_id'], 'User Created', 'User', $user_id, "Created new $role: $full_name");
                return ['success' => true, 'message' => ucfirst($role) . ' created successfully', 'user_id' => $user_id];
            } else {
                return ['success' => false, 'message' => 'Failed to create user'];
            }
        } catch (Exception $e) {
            error_log("Add User Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Edit existing user
     */
    private function editUser() {
        try {
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;
            $full_name = isset($_POST['full_name']) ? sanitizeInput($_POST['full_name']) : null;
            $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : null;
            $specialization = isset($_POST['specialization']) ? sanitizeInput($_POST['specialization']) : null;
            $license_number = isset($_POST['license_number']) ? sanitizeInput($_POST['license_number']) : null;

            if (!$user_id || !$full_name || !$email) {
                return ['success' => false, 'message' => 'Missing required fields'];
            }

            // Check if email is already in use by another user
            if ($this->userModel->emailExists($email, $user_id)) {
                return ['success' => false, 'message' => 'Email already in use by another user'];
            }

            $result = $this->userModel->updateUser($user_id, $full_name, $email, $specialization, $license_number);

            if ($result) {
                logSystemAction($_SESSION['user_id'], 'User Updated', 'User', $user_id, "Updated user: $full_name");
                return ['success' => true, 'message' => 'User updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update user'];
            }
        } catch (Exception $e) {
            error_log("Edit User Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete user
     */
    private function deleteUser() {
        try {
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

            if (!$user_id) {
                return ['success' => false, 'message' => 'User ID required'];
            }

            if ($user_id == $_SESSION['user_id']) {
                return ['success' => false, 'message' => 'You cannot delete your own account'];
            }

            $result = $this->userModel->deleteUser($user_id);

            if ($result) {
                logSystemAction($_SESSION['user_id'], 'User Deleted', 'User', $user_id, "Deleted user ID: $user_id");
                return ['success' => true, 'message' => 'User deleted successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to delete user'];
            }
        } catch (Exception $e) {
            error_log("Delete User Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Toggle user active/inactive status
     */
    private function toggleUserStatus() {
        try {
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

            if (!$user_id) {
                return ['success' => false, 'message' => 'User ID required'];
            }

            if ($user_id == $_SESSION['user_id']) {
                return ['success' => false, 'message' => 'You cannot deactivate your own account'];
            }

            $user = $this->userModel->getUserById($user_id);
            if (!$user) {
                return ['success' => false, 'message' => 'User not found'];
            }

            $is_active = $user['is_active'];

            if ($is_active) {
                $result = $this->userModel->deactivateUser($user_id);
                $action = 'Deactivated';
            } else {
                $result = $this->userModel->activateUser($user_id);
                $action = 'Activated';
            }

            if ($result) {
                logSystemAction($_SESSION['user_id'], 'User Status Changed', 'User', $user_id, "$action user: " . $user['full_name']);
                return ['success' => true, 'message' => "User " . strtolower($action) . " successfully", 'is_active' => !$is_active];
            } else {
                return ['success' => false, 'message' => 'Failed to update user status'];
            }
        } catch (Exception $e) {
            error_log("Toggle User Status Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get user data for editing
     */
    private function getUser() {
        try {
            $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : null;

            if (!$user_id) {
                return ['success' => false, 'message' => 'User ID required'];
            }

            $stmt = $this->db->prepare("
                SELECT id, username, email, full_name, role, specialization, license_number, is_active
                FROM users
                WHERE id = :id
            ");
            $stmt->execute([':id' => $user_id]);
            $user = $stmt->fetch();

            if ($user) {
                return ['success' => true, 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'User not found'];
            }
        } catch (Exception $e) {
            error_log("Get User Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
