<?php
/**
 * AUTHENTICATION CONTROLLER
 * ============================================================================
 * Handles user login and session management
 * ============================================================================
 */

require_once __DIR__ . '/../config/db_config.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $db;
    private $userModel;
    
    public function __construct() {
        $this->db = getDBConnection();
        $this->userModel = new User($this->db);
    }
    
    /**
     * Handle login form submission
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return false;
        }
        
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        if (empty($username) || empty($password)) {
            return false;
        }
        
        // Authenticate user
        $user = $this->userModel->authenticate($username, $password);
        
        if ($user) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            
            // Log the login action
            logSystemAction($user['id'], 'User Login', 'User', $user['id'], 'User successfully authenticated');
            
            // Redirect to appropriate dashboard
            if ($user['role'] === 'admin') {
                header('Location: /CANCER/index.php?page=admin-dashboard');
            } else {
                header('Location: /CANCER/index.php?page=doctor-dashboard');
            }
            exit;
        } else {
            // Login failed - redirect back with error
            header('Location: /CANCER/index.php?page=login&error=1');
            exit;
        }
    }
    
    /**
     * Handle logout
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            logSystemAction($_SESSION['user_id'], 'User Logout', 'User', $_SESSION['user_id'], 'User session ended');
        }
        
        session_destroy();
        header('Location: /CANCER/index.php?page=login');
        exit;
    }
}

// Handle incoming requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new AuthController();
    $controller->login();
}
?>
