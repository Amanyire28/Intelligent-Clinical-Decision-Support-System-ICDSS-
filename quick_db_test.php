<?php
/**
 * Quick Database Error Diagnostic
 */
require_once __DIR__ . '/config/db_config.php';

try {
    $db = getDBConnection();
    
    echo "<h2>1. Checking patient_outcomes table columns:</h2>";
    $result = $db->query("SHOW COLUMNS FROM patient_outcomes");
    $columns = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
    echo "<h2>2. Finding a pending assessment:</h2>";
    $stmt = $db->prepare("
        SELECT a.id, a.patient_id FROM assessments a
        LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
        WHERE po.id IS NULL LIMIT 1
    ");
    $stmt->execute();
    $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($assessment) {
        echo "Assessment ID: " . $assessment['id'] . ", Patient ID: " . $assessment['patient_id'] . "<br>";
        
        echo "<h2>3. Attempting INSERT with error reporting:</h2>";
        try {
            $sql = "INSERT INTO patient_outcomes (
                patient_id, assessment_id, final_diagnosis, cancer_stage,
                cancer_type, treatment_plan, treatment_urgency, 
                clinical_findings, recommendations, follow_up_date, tumor_location,
                outcome_date, follow_up_status, survival_status, years_survived, notes
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";
            
            $stmt = $db->prepare($sql);
            $result = $stmt->execute([
                $assessment['patient_id'],     // patient_id
                $assessment['id'],              // assessment_id
                'Benign',                       // final_diagnosis
                null,                           // cancer_stage
                null,                           // cancer_type
                'Monitoring',                   // treatment_plan
                'Non-urgent',                   // treatment_urgency
                'Test findings',                // clinical_findings
                'Test recommendations',         // recommendations
                date('Y-m-d'),                 // follow_up_date
                null,                           // tumor_location
                date('Y-m-d'),                 // outcome_date
                null,                           // follow_up_status
                'Alive',                        // survival_status
                null,                           // years_survived
                null                            // notes
            ]);
            
            if ($result) {
                echo "<p style='color: green; font-weight: bold;'>✅ SUCCESS! Inserted ID: " . $db->lastInsertId() . "</p>";
            } else {
                echo "<p style='color: red;'><strong>Error Info:</strong></p>";
                echo "<pre>" . json_encode($stmt->errorInfo(), JSON_PRETTY_PRINT) . "</pre>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'><strong>Exception:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>Code:</strong> " . $e->getCode() . "</p>";
        }
    } else {
        echo "No pending assessments found.";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . $e->getMessage() . "</p>";
}
?>
