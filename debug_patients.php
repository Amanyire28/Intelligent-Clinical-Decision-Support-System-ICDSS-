<?php
// Simple debug script to check if patients exist
session_start();
require_once __DIR__ . '/config/db_config.php';

if (!isset($_SESSION['user_id'])) {
    die('Not authenticated');
}

$db = getDBConnection();

// Get all patients
$stmt = $db->prepare("SELECT COUNT(*) as total FROM patients");
$stmt->execute();
$result = $stmt->fetch();
$total = $result['total'];

echo "Total Patients in Database: $total\n\n";

if ($total > 0) {
    echo "First 10 Patients:\n";
    $stmt = $db->prepare("SELECT id, first_name, last_name, medical_record_number FROM patients LIMIT 10");
    $stmt->execute();
    $patients = $stmt->fetchAll();
    
    foreach ($patients as $p) {
        echo "  ID: {$p['id']}, Name: {$p['first_name']} {$p['last_name']}, MRN: {$p['medical_record_number']}\n";
    }
    
    echo "\n\nTesting search for 'a':\n";
    $search = "%a%";
    $stmt = $db->prepare("
        SELECT id, first_name, last_name, medical_record_number
        FROM patients 
        WHERE first_name LIKE :search OR last_name LIKE :search OR medical_record_number LIKE :search
        ORDER BY first_name, last_name ASC
        LIMIT 50
    ");
    $stmt->execute([':search' => $search]);
    $results = $stmt->fetchAll();
    echo "Found: " . count($results) . " results\n";
    foreach ($results as $p) {
        echo "  - {$p['first_name']} {$p['last_name']}\n";
    }
}
?>
