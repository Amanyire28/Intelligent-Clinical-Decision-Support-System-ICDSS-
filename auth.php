<?php
/**
 * ============================================================================
 * AUTHENTICATION HANDLER
 * auth.php - Processes login/logout requests
 * ============================================================================
 */

session_start();

// Load configuration and controllers
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle login form submission
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            header('Location: index.php?page=login&error=1');
            exit;
        }
        
        // Get database connection and authenticate
        try {
            $db = getDBConnection();
            $userModel = new User($db);
            
            // Authenticate user against database
            $user = $userModel->authenticate($username, $password);
        } catch (Exception $e) {
            error_log("Login Error: " . $e->getMessage());
            header('Location: index.php?page=login&error=1');
            exit;
        }
        
        if ($user) {
            // Login successful - set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            
            // Log the login action
            logSystemAction($user['id'], 'User Login', 'User', $user['id'], 'User successfully authenticated');
            
            // Redirect to appropriate dashboard
            if ($user['role'] === 'admin') {
                header('Location: index.php?page=admin-dashboard');
            } else {
                header('Location: index.php?page=doctor-dashboard');
            }
            exit;
        } else {
            // Login failed - redirect back with error
            header('Location: index.php?page=login&error=1');
            exit;
        }
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Handle logout request
    if (isset($_SESSION['user_id'])) {
        logSystemAction($_SESSION['user_id'], 'User Logout', 'User', $_SESSION['user_id'], 'User session ended');
    }
    session_destroy();
    header('Location: index.php?page=login');
    exit;
}

// If we reach here, something went wrong
header('Location: index.php?page=login');
exit;
?>
