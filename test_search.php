<?php
session_start();
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Patient.php';

// Check if logged in
if (!isset($_SESSION['user_id'])) {
    die('Not logged in');
}

$db = getDBConnection();
$patientModel = new Patient($db);

// Get all patients
$stmt = $db->prepare("SELECT COUNT(*) as total FROM patients");
$stmt->execute();
$result = $stmt->fetch();
echo "Total patients in database: " . $result['total'] . "\n";

// Try searching for "sam" (if Samuel exists)
$patients = $patientModel->searchPatients('sam');
echo "Search results for 'sam': " . count($patients) . "\n";
if (!empty($patients)) {
    foreach ($patients as $patient) {
        echo "  - " . $patient['first_name'] . " " . $patient['last_name'] . "\n";
    }
}

// Try searching for empty
$patients = $patientModel->searchPatients('');
echo "Search results for empty: " . count($patients) . "\n";

// List all patients
$stmt = $db->prepare("SELECT id, first_name, last_name FROM patients LIMIT 10");
$stmt->execute();
$all = $stmt->fetchAll();
echo "First 10 patients:\n";
foreach ($all as $p) {
    echo "  - ID: " . $p['id'] . ", Name: " . $p['first_name'] . " " . $p['last_name'] . "\n";
}
?>
