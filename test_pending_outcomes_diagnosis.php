<?php
// Start session to match normal app flow
session_start();

// Connect to database
require_once __DIR__ . '/config/db_config.php';

try {
    $db = getDBConnection();
    
    echo "<h2>Pending Outcomes Diagnostic</h2>";
    echo "<hr>";
    
    // 1. Check all assessments
    echo "<h3>1. Total Assessments in Database:</h3>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM assessments");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total: " . $result['count'] . "<br>";
    
    // 2. Check patient_outcomes table
    echo "<h3>2. Patient Outcomes Recorded:</h3>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM patient_outcomes");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Total outcomes recorded: " . $result['count'] . "<br>";
    
    // 3. Show doctor ID 1's assessments
    echo "<h3>3. Doctor ID 1's Assessments:</h3>";
    $stmt = $db->prepare("
        SELECT a.id, a.assessment_date, p.first_name, p.last_name, 
               (SELECT COUNT(*) FROM patient_outcomes WHERE assessment_id = a.id) as has_outcome
        FROM assessments a
        LEFT JOIN patients p ON a.patient_id = p.id
        WHERE a.doctor_id = 1
        ORDER BY a.assessment_date DESC
    ");
    $stmt->execute();
    $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($assessments) . " assessments for doctor 1<br>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Assessment ID</th><th>Patient</th><th>Date</th><th>Has Outcome</th></tr>";
    foreach ($assessments as $a) {
        echo "<tr>";
        echo "<td>" . $a['id'] . "</td>";
        echo "<td>" . htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) . "</td>";
        echo "<td>" . $a['assessment_date'] . "</td>";
        echo "<td>" . ($a['has_outcome'] ? '✅ YES' : '❌ NO (Pending)') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // 4. Show PENDING outcomes (what the query should return)
    echo "<h3>4. PENDING Outcomes (Assessments without outcome):</h3>";
    $stmt = $db->prepare("
        SELECT a.id, a.assessment_date, a.patient_id, p.first_name, p.last_name, 
               rr.risk_level, rr.risk_score
        FROM assessments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN risk_results rr ON a.id = rr.assessment_id
        LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
        WHERE a.doctor_id = 1 AND po.id IS NULL
        ORDER BY a.assessment_date DESC
        LIMIT 20
    ");
    $stmt->execute();
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Found " . count($pending) . " pending outcomes<br>";
    if (count($pending) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Assessment ID</th><th>Patient</th><th>Assessment Date</th><th>Risk Level</th><th>Risk Score</th></tr>";
        foreach ($pending as $p) {
            echo "<tr>";
            echo "<td>" . $p['id'] . "</td>";
            echo "<td>" . htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) . "</td>";
            echo "<td>" . $p['assessment_date'] . "</td>";
            echo "<td>" . htmlspecialchars($p['risk_level']) . "</td>";
            echo "<td>" . htmlspecialchars($p['risk_score']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: green; font-weight: bold;'>✅ No pending outcomes - All assessments have been recorded!</p>";
    }
    
    // 5. Show the raw outcome data for doctor 1's assessments
    echo "<h3>5. Patient Outcomes Detail (for doctor 1's assessments):</h3>";
    $stmt = $db->prepare("
        SELECT po.*, a.doctor_id
        FROM patient_outcomes po
        LEFT JOIN assessments a ON po.assessment_id = a.id
        WHERE a.doctor_id = 1
    ");
    $stmt->execute();
    $outcomes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Found " . count($outcomes) . " outcomes for doctor 1<br>";
    if (count($outcomes) > 0) {
        echo "<pre>" . print_r($outcomes, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . htmlspecialchars($e->getMessage());
}
?>
<style>
    body { font-family: Arial; margin: 20px; }
    h2 { color: #333; }
    h3 { color: #666; margin-top: 20px; }
    table { border-collapse: collapse; margin: 10px 0; }
    td { padding: 8px; border: 1px solid #ddd; }
    th { background-color: #f0f0f0; padding: 8px; text-align: left; }
    pre { background-color: #f5f5f5; padding: 10px; overflow-x: auto; }
</style>
