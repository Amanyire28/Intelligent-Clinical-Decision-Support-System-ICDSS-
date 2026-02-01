<?php
/**
 * DATABASE CONFIGURATION
 * ============================================================================
 * Centralized database connection for ICDSS system
 * ============================================================================
 */

// Database credentials (update with your XAMPP MySQL settings)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Default XAMPP password is empty
define('DB_NAME', 'icdss_cancer_db');
define('DB_PORT', 3306);

// Connection options
define('DB_CHARSET', 'utf8mb4');

/**
 * Establish database connection using PDO
 * PDO provides prepared statements protection against SQL injection
 */
function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        $pdo = new PDO(
            $dsn,
            DB_USER,
            DB_PASS,
            array(
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            )
        );
        
        return $pdo;
    } catch (PDOException $e) {
        die("Database Connection Error: " . $e->getMessage());
    }
}

/**
 * Helper function to log system actions (audit trail)
 * Useful for compliance and debugging
 */
function logSystemAction($user_id, $action, $resource_type = null, $resource_id = null, $description = null) {
    try {
        $db = getDBConnection();
        $stmt = $db->prepare("
            INSERT INTO system_logs (user_id, action, resource_type, resource_id, description)
            VALUES (:user_id, :action, :resource_type, :resource_id, :description)
        ");
        
        $stmt->execute([
            ':user_id' => $user_id,
            ':action' => $action,
            ':resource_type' => $resource_type,
            ':resource_id' => $resource_id,
            ':description' => $description
        ]);
    } catch (PDOException $e) {
        error_log("Logging Error: " . $e->getMessage());
    }
}
?>
