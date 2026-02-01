<?php
/**
 * Complete Workflow Test
 * Tests the entire assessment → outcome recording → removal from pending cycle
 */

session_start();
require_once __DIR__ . '/config/db_config.php';

// Simulate doctor login
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'doctor';
$_SESSION['user_name'] = 'Dr. Test';

echo "<h1>Complete Workflow Test</h1>";
echo "<hr>";

try {
    $db = getDBConnection();
    
    // Step 1: Check pending outcomes for doctor 1
    echo "<h2>STEP 1: Check Pending Outcomes Before</h2>";
    $stmt = $db->prepare("
        SELECT COUNT(*) as count FROM assessments a
        LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
        WHERE a.doctor_id = 1 AND po.id IS NULL
    ");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $pending_before = $result['count'];
    echo "<p>Pending outcomes: <strong>$pending_before</strong></p>";
    
    // Step 2: List all pending assessments
    echo "<h2>STEP 2: Pending Assessments</h2>";
    $stmt = $db->prepare("
        SELECT a.id, a.assessment_date, p.first_name, p.last_name
        FROM assessments a
        LEFT JOIN patients p ON a.patient_id = p.id
        LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
        WHERE a.doctor_id = 1 AND po.id IS NULL
        ORDER BY a.assessment_date DESC
        LIMIT 5
    ");
    $stmt->execute();
    $pending = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($pending) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>Assessment ID</th><th>Patient</th><th>Date</th><th>Status</th></tr>";
        foreach ($pending as $p) {
            echo "<tr>";
            echo "<td><strong>" . $p['id'] . "</strong></td>";
            echo "<td>" . htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) . "</td>";
            echo "<td>" . date('M d, Y', strtotime($p['assessment_date'])) . "</td>";
            echo "<td style='color: red; font-weight: bold;'>❌ PENDING</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        $first_pending = $pending[0];
        echo "<p style='margin-top: 10px;'><strong>👉 Next step:</strong> Record outcome for assessment ID <strong>" . $first_pending['id'] . "</strong></p>";
        
        // Step 3: Simulate recording an outcome
        echo "<h2>STEP 3: Recording Outcome for Assessment #" . $first_pending['id'] . "</h2>";
        
        $outcome_data = [
            'assessment_id' => $first_pending['id'],
            'final_diagnosis' => 'Benign',
            'cancer_type' => NULL,
            'cancer_stage' => NULL,
            'tumor_location' => NULL,
            'treatment_plan' => 'Monitoring',
            'treatment_urgency' => 'Non-urgent',
            'clinical_findings' => 'Assessment indicates benign findings',
            'recommendations' => 'Continue routine monitoring',
            'follow_up_date' => date('Y-m-d', strtotime('+3 months'))
        ];
        
        $stmt = $db->prepare("
            INSERT INTO patient_outcomes 
            (assessment_id, final_diagnosis, cancer_type, cancer_stage, tumor_location, treatment_plan, treatment_urgency, clinical_findings, recommendations, follow_up_date, recorded_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([
            $outcome_data['assessment_id'],
            $outcome_data['final_diagnosis'],
            $outcome_data['cancer_type'],
            $outcome_data['cancer_stage'],
            $outcome_data['tumor_location'],
            $outcome_data['treatment_plan'],
            $outcome_data['treatment_urgency'],
            $outcome_data['clinical_findings'],
            $outcome_data['recommendations'],
            $outcome_data['follow_up_date']
        ]);
        
        echo "<p style='color: green; font-weight: bold;'>✅ Outcome recorded successfully!</p>";
        echo "<p>Outcome ID: " . $db->lastInsertId() . "</p>";
        
        // Step 4: Check pending outcomes again
        echo "<h2>STEP 4: Check Pending Outcomes After</h2>";
        $stmt = $db->prepare("
            SELECT COUNT(*) as count FROM assessments a
            LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
            WHERE a.doctor_id = 1 AND po.id IS NULL
        ");
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $pending_after = $result['count'];
        echo "<p>Pending outcomes: <strong style='color: green;'>$pending_after</strong></p>";
        
        if ($pending_after < $pending_before) {
            echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Assessment removed from pending list after recording outcome</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ ERROR: Pending count did not decrease</p>";
        }
        
        // Step 5: Verify the outcome was recorded
        echo "<h2>STEP 5: Verify Outcome Data</h2>";
        $stmt = $db->prepare("
            SELECT * FROM patient_outcomes 
            WHERE assessment_id = ?
            ORDER BY recorded_at DESC
            LIMIT 1
        ");
        $stmt->execute([$first_pending['id']]);
        $outcome = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($outcome) {
            echo "<table border='1' cellpadding='10'>";
            foreach ($outcome as $key => $value) {
                echo "<tr>";
                echo "<td><strong>$key</strong></td>";
                echo "<td>" . htmlspecialchars($value ?? '(null)') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
    } else {
        echo "<p style='color: orange;'>⚠️ No pending outcomes found for doctor 1.</p>";
        echo "<p>This means either:</p>";
        echo "<ul>";
        echo "<li>All assessments have been recorded</li>";
        echo "<li>Doctor 1 has no assessments</li>";
        echo "</ul>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #fee; padding: 10px; border: 1px solid red;'>";
    echo "ERROR: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1, h2 { color: #333; }
    table { background: white; border-collapse: collapse; margin: 10px 0; }
    th { background: #f0f0f0; text-align: left; }
    td { padding: 8px; }
</style>
