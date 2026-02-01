<?php
/**
 * Test Outcome Recording
 * Simulates the complete workflow of recording an outcome
 */
session_start();
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/PatientOutcome.php';
require_once __DIR__ . '/models/Assessment.php';

// Simulate doctor login
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'doctor';
$_SESSION['user_name'] = 'Dr. Test';

echo "<h1>🧪 Test Outcome Recording</h1>";
echo "<hr>";

try {
    $db = getDBConnection();
    
    // Get a pending assessment
    $stmt = $db->prepare("
        SELECT a.id, a.patient_id, p.first_name, p.last_name
        FROM assessments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
        WHERE a.doctor_id = 1 AND po.id IS NULL
        LIMIT 1
    ");
    $stmt->execute();
    $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$assessment) {
        echo "<p style='color: orange;'>⚠️ No pending assessments found for doctor 1</p>";
        exit;
    }
    
    echo "<h2>Test Data</h2>";
    echo "Assessment ID: <strong>" . $assessment['id'] . "</strong><br>";
    echo "Patient: <strong>" . htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']) . "</strong><br>";
    echo "Patient ID: <strong>" . $assessment['patient_id'] . "</strong><br><br>";
    
    // Create outcome data matching what the form sends
    $outcomeData = [
        'final_diagnosis' => 'Malignant',
        'cancer_type' => 'Squamous Cell Carcinoma',
        'cancer_stage' => 'Stage 2',
        'tumor_location' => 'Larynx',
        'treatment_plan' => 'Surgery, Chemotherapy',
        'treatment_urgency' => 'Urgent',
        'clinical_findings' => 'Test clinical findings for assessment',
        'recommendations' => 'Refer to oncology for treatment planning',
        'follow_up_date' => date('Y-m-d', strtotime('+30 days')),
        'outcome_date' => date('Y-m-d'),
        'follow_up_status' => NULL,
        'survival_status' => 'Alive',
        'years_survived' => NULL,
        'notes' => NULL
    ];
    
    echo "<h2>Recording Outcome Data</h2>";
    echo "<pre>" . json_encode($outcomeData, JSON_PRETTY_PRINT) . "</pre>";
    echo "<br>";
    
    // Record the outcome
    $outcomeModel = new PatientOutcome($db);
    $outcomeId = $outcomeModel->recordOutcome($assessment['patient_id'], $assessment['id'], $outcomeData);
    
    if ($outcomeId) {
        echo "<h2 style='color: green;'>✅ SUCCESS!</h2>";
        echo "<p>Outcome recorded with ID: <strong>$outcomeId</strong></p>";
        
        // Verify the outcome was saved
        echo "<h2>Verification</h2>";
        $stmt = $db->prepare("SELECT * FROM patient_outcomes WHERE id = ?");
        $stmt->execute([$outcomeId]);
        $saved = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($saved) {
            echo "<table border='1' cellpadding='10' style='margin: 10px 0;'>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            foreach ($saved as $key => $value) {
                echo "<tr>";
                echo "<td><strong>$key</strong></td>";
                echo "<td>" . htmlspecialchars($value ?? '(null)') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        // Check pending outcomes count
        echo "<h2>Pending Outcomes Check</h2>";
        $stmt = $db->prepare("
            SELECT COUNT(*) as count FROM assessments a
            LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
            WHERE a.doctor_id = 1 AND po.id IS NULL
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Remaining pending outcomes for doctor 1: <strong style='color: green;'>" . $result['count'] . "</strong></p>";
        
    } else {
        echo "<h2 style='color: red;'>❌ FAILED!</h2>";
        echo "<p>Failed to record outcome. Check error logs.</p>";
        
        // Show database schema
        echo "<h2>Database Schema Check</h2>";
        $stmt = $db->prepare("DESCRIBE patient_outcomes");
        $stmt->execute();
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
        foreach ($columns as $col) {
            echo "<tr>";
            echo "<td><strong>" . $col['Field'] . "</strong></td>";
            echo "<td>" . $col['Type'] . "</td>";
            echo "<td>" . $col['Null'] . "</td>";
            echo "<td>" . ($col['Default'] ?? 'N/A') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #fee; padding: 10px; border: 1px solid red;'>";
    echo "<h3>ERROR</h3>";
    echo htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1, h2 { color: #333; }
    table { background: white; border-collapse: collapse; }
    th { background: #f0f0f0; text-align: left; }
    td { padding: 8px; }
    pre { background: white; padding: 10px; border: 1px solid #ddd; overflow-x: auto; }
    hr { border: none; border-top: 2px solid #ccc; margin: 20px 0; }
</style>
