<?php
/**
 * Debug Authentication Handler
 */

session_start();

// Load configuration and models
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = $_POST['password'];
        
        error_log("DEBUG: Login attempt for username: " . $username);
        
        try {
            $db = getDBConnection();
            error_log("DEBUG: Database connection successful");
            
            // Direct query to debug
            $stmt = $db->prepare("SELECT id, username, password_hash, full_name, role FROM users WHERE username = :username AND is_active = TRUE");
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch();
            
            if (!$user) {
                error_log("DEBUG: User not found: " . $username);
                header('Location: index.php?page=login&error=1');
                exit;
            }
            
            error_log("DEBUG: User found: " . $user['username']);
            error_log("DEBUG: Password hash from DB: " . substr($user['password_hash'], 0, 20) . "...");
            
            $verify = password_verify($password, $user['password_hash']);
            error_log("DEBUG: Password verify result: " . ($verify ? "TRUE" : "FALSE"));
            
            if ($verify) {
                error_log("DEBUG: Authentication successful for: " . $username);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Redirect to appropriate dashboard based on role
                if ($user['role'] === 'admin') {
                    header('Location: index.php?page=admin-dashboard');
                } else {
                    header('Location: index.php?page=doctor-dashboard');
                }
                exit;
            } else {
                error_log("DEBUG: Password verification failed");
                header('Location: index.php?page=login&error=1');
                exit;
            }
        } catch (Exception $e) {
            error_log("DEBUG: Exception caught: " . $e->getMessage());
            header('Location: index.php?page=login&error=1');
            exit;
        }
    }
}

header('Location: index.php?page=login');
exit;
?>
