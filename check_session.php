<?php
session_start();

// Get session info
$session_info = [
    'Session ID' => session_id(),
    'Session Status' => session_status(),
    'Session Data' => $_SESSION,
    'GET Parameters' => $_GET,
    'User ID' => $_SESSION['user_id'] ?? 'NOT SET',
    'User Role' => $_SESSION['user_role'] ?? 'NOT SET',
    'User Name' => $_SESSION['user_name'] ?? 'NOT SET'
];

header('Content-Type: application/json');
echo json_encode($session_info, JSON_PRETTY_PRINT);
?>
