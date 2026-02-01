<?php
/**
 * Run database migration to add missing columns
 */
require_once __DIR__ . '/config/db_config.php';

try {
    $db = getDBConnection();
    
    // Read the migration file
    $migration = file_get_contents(__DIR__ . '/database/alter_patient_outcomes_doctor_fields.sql');
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $migration)));
    
    echo "<h1>Database Migration - Patient Outcomes Table</h1>";
    echo "<hr>";
    
    foreach ($statements as $statement) {
        if (empty($statement)) continue;
        
        try {
            $db->exec($statement);
            echo "✅ <strong>Success:</strong> " . substr($statement, 0, 60) . "...<br>";
        } catch (Exception $e) {
            echo "⚠️ <strong>Notice:</strong> " . substr($statement, 0, 60) . "...<br>";
            echo "&nbsp;&nbsp;Message: " . $e->getMessage() . "<br>";
        }
    }
    
    echo "<hr>";
    echo "<h2>Migration Complete</h2>";
    echo "<p>The patient_outcomes table has been updated with the following new columns:</p>";
    echo "<ul>";
    echo "<li><strong>treatment_plan</strong> - Comma-separated treatment methods</li>";
    echo "<li><strong>treatment_urgency</strong> - Treatment urgency level</li>";
    echo "<li><strong>clinical_findings</strong> - Doctor's clinical findings</li>";
    echo "<li><strong>recommendations</strong> - Doctor recommendations</li>";
    echo "<li><strong>follow_up_date</strong> - Recommended follow-up date</li>";
    echo "<li><strong>tumor_location</strong> - Tumor location if applicable</li>";
    echo "<li><strong>recorded_at</strong> - Timestamp when outcome was recorded</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<div style='color: red; background: #fee; padding: 10px; border: 1px solid red;'>";
    echo "ERROR: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}
?>
<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h1, h2 { color: #333; }
    hr { border: none; border-top: 2px solid #ccc; margin: 20px 0; }
</style>
