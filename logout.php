<?php
/**
 * LOGOUT HANDLER
 * Destroys session and redirects to login
 */

session_start();

// Log the logout action if user is logged in
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/config/db_config.php';
    logSystemAction($_SESSION['user_id'], 'User Logout', 'User', $_SESSION['user_id'], 'User session ended');
}

// Destroy session
session_destroy();

// Redirect to login
header('Location: index.php?page=login');
exit;
?>
