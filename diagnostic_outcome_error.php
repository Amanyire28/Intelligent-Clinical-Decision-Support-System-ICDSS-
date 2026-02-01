<?php
/**
 * Diagnostic - Test outcome recording with detailed error logging
 */
session_start();
require_once __DIR__ . '/config/db_config.php';

// Set user session
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'doctor';
$_SESSION['user_name'] = 'Dr. Test';

echo "<h1>Outcome Recording Diagnostic</h1>";
echo "<hr>";

try {
    $db = getDBConnection();
    
    // Step 1: Check table structure
    echo "<h2>1. Table Structure Check</h2>";
    $stmt = $db->query("DESCRIBE patient_outcomes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Step 2: Find a pending assessment
    echo "<h2>2. Find Pending Assessment</h2>";
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
        echo "<p style='color: orange;'>⚠️ No pending assessment found for doctor 1</p>";
        exit;
    }
    
    echo "<p>Found pending assessment ID: <strong>" . $assessment['id'] . "</strong></p>";
    echo "<p>Patient: " . htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']) . "</p>";
    
    // Step 3: Prepare outcome data
    echo "<h2>3. Test Outcome Recording</h2>";
    $data = [
        'final_diagnosis' => 'Benign',
        'cancer_stage' => null,
        'cancer_type' => null,
        'treatment_plan' => 'Monitoring Only',
        'treatment_urgency' => 'Non-urgent',
        'clinical_findings' => 'Test clinical findings',
        'recommendations' => 'Test recommendations',
        'follow_up_date' => date('Y-m-d', strtotime('+3 months')),
        'tumor_location' => null,
        'outcome_date' => date('Y-m-d'),
        'follow_up_status' => null,
        'survival_status' => 'Alive',
        'years_survived' => null,
        'notes' => null
    ];
    
    echo "<p>Attempting to insert with data:</p>";
    echo "<pre>" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
    
    // Step 4: Try direct insert
    echo "<h2>4. Direct Insert Test</h2>";
    try {
        $stmt = $db->prepare("
            INSERT INTO patient_outcomes (
                patient_id, assessment_id, final_diagnosis, cancer_stage,
                cancer_type, treatment_plan, treatment_urgency, 
                clinical_findings, recommendations, follow_up_date, tumor_location,
                outcome_date, follow_up_status, survival_status, years_survived, notes, recorded_at
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
            )
        ");
        
        $result = $stmt->execute([
            $assessment['patient_id'],
            $assessment['id'],
            $data['final_diagnosis'],
            $data['cancer_stage'],
            $data['cancer_type'],
            $data['treatment_plan'],
            $data['treatment_urgency'],
            $data['clinical_findings'],
            $data['recommendations'],
            $data['follow_up_date'],
            $data['tumor_location'],
            $data['outcome_date'],
            $data['follow_up_status'],
            $data['survival_status'],
            $data['years_survived'],
            $data['notes']
        ]);
        
        if ($result) {
            $outcome_id = $db->lastInsertId();
            echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Outcome recorded with ID: $outcome_id</p>";
        } else {
            echo "<p style='color: red; font-weight: bold;'>❌ Execute returned false</p>";
            echo "<p>Error Info: " . json_encode($stmt->errorInfo()) . "</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red; font-weight: bold;'>❌ Exception during insert:</p>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>Error Code: " . $e->getCode() . "</p>";
    }
    
    // Step 5: Verify data was inserted
    echo "<h2>5. Verify Inserted Data</h2>";
    $stmt = $db->prepare("SELECT * FROM patient_outcomes WHERE assessment_id = ?");
    $stmt->execute([$assessment['id']]);
    $outcome = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($outcome) {
        echo "<p style='color: green;'>✅ Outcome found in database!</p>";
        echo "<pre>" . json_encode($outcome, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "</pre>";
    } else {
        echo "<p style='color: orange;'>⚠️ Outcome not found in database after insert</p>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #fee; padding: 10px; border: 1px solid red;'>";
    echo "ERROR: " . htmlspecialchars($e->getMessage());
    echo "<br>Code: " . $e->getCode();
    echo "</div>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
    h1, h2 { color: #333; }
    table { background: white; border-collapse: collapse; margin: 10px 0; }
    th, td { padding: 8px; border: 1px solid #ddd; text-align: left; }
    th { background: #f0f0f0; font-weight: bold; }
    pre { background: #fff; border: 1px solid #ddd; padding: 10px; overflow-x: auto; }
</style>
