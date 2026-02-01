<?php
// Direct database query to check pending outcomes
require_once __DIR__ . '/config/db_config.php';

try {
    $db = getDBConnection();
    
    echo "<h1>CANCER System - Database Status Check</h1>";
    echo "<hr>";
    
    // Check all doctors and their pending outcomes
    echo "<h2>All Doctors and Their Pending Outcomes:</h2>";
    
    $stmt = $db->query("SELECT DISTINCT doctor_id FROM assessments ORDER BY doctor_id");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($doctors as $doc) {
        $doctor_id = $doc['doctor_id'];
        
        // Get doctor name
        $stmt = $db->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
        $stmt->execute([$doctor_id]);
        $doctor = $stmt->fetch(PDO::FETCH_ASSOC);
        $doctor_name = ($doctor) ? htmlspecialchars($doctor['first_name'] . ' ' . $doctor['last_name']) : "Unknown";
        
        echo "<h3>Doctor #$doctor_id: $doctor_name</h3>";
        
        // Get pending outcomes for this doctor
        $stmt = $db->prepare("
            SELECT 
                a.id,
                a.assessment_date,
                p.first_name,
                p.last_name,
                rr.risk_level,
                rr.risk_score,
                (SELECT COUNT(*) FROM patient_outcomes WHERE assessment_id = a.id) as has_outcome
            FROM assessments a
            LEFT JOIN patients p ON a.patient_id = p.id
            LEFT JOIN risk_results rr ON a.id = rr.assessment_id
            WHERE a.doctor_id = ?
            ORDER BY a.assessment_date DESC
        ");
        $stmt->execute([$doctor_id]);
        $assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<strong>Total assessments: " . count($assessments) . "</strong><br>";
        
        $pending_count = 0;
        $with_outcome_count = 0;
        
        echo "<table border='1' cellpadding='8' style='margin: 10px 0;'>";
        echo "<tr>";
        echo "<th>Assessment ID</th>";
        echo "<th>Patient</th>";
        echo "<th>Date</th>";
        echo "<th>Risk Level</th>";
        echo "<th>Risk Score</th>";
        echo "<th>Outcome Status</th>";
        echo "</tr>";
        
        foreach ($assessments as $a) {
            if ($a['has_outcome']) {
                $with_outcome_count++;
                $status = "✅ Recorded";
                $style = "color: green;";
            } else {
                $pending_count++;
                $status = "❌ PENDING";
                $style = "color: red; font-weight: bold;";
            }
            
            echo "<tr>";
            echo "<td>" . $a['id'] . "</td>";
            echo "<td>" . htmlspecialchars($a['first_name'] . ' ' . $a['last_name']) . "</td>";
            echo "<td>" . date('M d, Y H:i', strtotime($a['assessment_date'])) . "</td>";
            echo "<td>" . htmlspecialchars($a['risk_level']) . "</td>";
            echo "<td>" . number_format($a['risk_score'], 1) . "</td>";
            echo "<td><span style='$style'>$status</span></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "Status: $pending_count pending, $with_outcome_count completed<br><br>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #fee; padding: 10px; border: 1px solid red;'>";
    echo "ERROR: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1, h2, h3 { color: #333; }
    table { background: white; border-collapse: collapse; }
    th { background: #f0f0f0; text-align: left; }
    hr { border: none; border-top: 2px solid #ccc; margin: 20px 0; }
</style>
