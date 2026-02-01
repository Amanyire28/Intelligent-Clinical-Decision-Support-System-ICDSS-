<?php
// Debug page to check pending outcomes
session_start();

// Set up test session
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['user_role'] = 'doctor';
    $_SESSION['user_name'] = 'Dr. Sarah Johnson';
}

require_once __DIR__ . '/config/db_config.php';

$db = getDBConnection();

// Check 1: Get all assessments
echo "<h2>All Assessments</h2>";
$stmt = $db->query("SELECT id, patient_id, assessment_date, doctor_id FROM assessments ORDER BY assessment_date DESC LIMIT 10");
$assessments = $stmt->fetchAll();
echo "Total: " . count($assessments) . "<br>";
foreach ($assessments as $a) {
    echo "ID: {$a['id']}, Patient: {$a['patient_id']}, Date: {$a['assessment_date']}, Doctor: {$a['doctor_id']}<br>";
}

// Check 2: Check which assessments have outcomes
echo "<h2>Assessments WITH Outcomes</h2>";
$stmt = $db->query("SELECT DISTINCT assessment_id FROM patient_outcomes");
$withOutcomes = $stmt->fetchAll();
$outcomeIds = array_map(fn($o) => $o['assessment_id'], $withOutcomes);
echo "IDs with outcomes: " . implode(', ', $outcomeIds) . "<br>";

// Check 3: Get pending outcomes (like the query in DoctorController)
echo "<h2>Pending Outcomes (Doctor ID: 1)</h2>";
$stmt = $db->prepare("
    SELECT 
        a.id,
        a.assessment_date,
        a.patient_id,
        p.first_name,
        p.last_name,
        rr.risk_level,
        rr.risk_score
    FROM assessments a
    LEFT JOIN patients p ON a.patient_id = p.id
    LEFT JOIN risk_results rr ON a.id = rr.assessment_id
    LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
    WHERE a.doctor_id = ? AND po.id IS NULL
    ORDER BY a.assessment_date DESC
    LIMIT 20
");
$stmt->execute([1]);
$pending = $stmt->fetchAll();
echo "Pending count: " . count($pending) . "<br>";
foreach ($pending as $p) {
    echo "ID: {$p['id']}, Patient: {$p['first_name']} {$p['last_name']}, Risk: {$p['risk_level']}<br>";
}

// Check 4: Check patient_outcomes table structure
echo "<h2>Patient Outcomes Table (first 5 records)</h2>";
$stmt = $db->query("SELECT * FROM patient_outcomes LIMIT 5");
$outcomes = $stmt->fetchAll();
echo "Total outcomes: " . count($outcomes) . "<br>";
if (!empty($outcomes)) {
    echo "Sample: " . json_encode($outcomes[0], JSON_PRETTY_PRINT) . "<br>";
}
?>
