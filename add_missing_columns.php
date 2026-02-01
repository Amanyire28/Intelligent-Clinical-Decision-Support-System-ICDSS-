<?php
/**
 * Manually add missing columns to patient_outcomes table
 */
require_once __DIR__ . '/config/db_config.php';

try {
    $db = getDBConnection();
    
    echo "<h1>Adding Missing Columns to patient_outcomes</h1>";
    echo "<hr>";
    
    $alterStatements = [
        "ALTER TABLE patient_outcomes ADD COLUMN treatment_plan VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated treatment methods'" => "treatment_plan",
        "ALTER TABLE patient_outcomes ADD COLUMN treatment_urgency VARCHAR(50) DEFAULT NULL COMMENT 'Treatment urgency level'" => "treatment_urgency",
        "ALTER TABLE patient_outcomes ADD COLUMN clinical_findings TEXT DEFAULT NULL COMMENT 'Doctor clinical findings'" => "clinical_findings",
        "ALTER TABLE patient_outcomes ADD COLUMN recommendations TEXT DEFAULT NULL COMMENT 'Doctor recommendations'" => "recommendations",
        "ALTER TABLE patient_outcomes ADD COLUMN follow_up_date DATE DEFAULT NULL COMMENT 'Recommended follow-up date'" => "follow_up_date",
        "ALTER TABLE patient_outcomes ADD COLUMN tumor_location VARCHAR(100) DEFAULT NULL COMMENT 'Location of tumor'" => "tumor_location",
        "ALTER TABLE patient_outcomes ADD COLUMN recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When outcome was recorded'" => "recorded_at",
    ];
    
    foreach ($alterStatements as $sql => $fieldName) {
        try {
            $db->exec($sql);
            echo "✅ Added column: <strong>$fieldName</strong><br>";
        } catch (PDOException $e) {
            // Column might already exist - that's okay
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "✓ Column already exists: <strong>$fieldName</strong><br>";
            } else {
                echo "⚠️ Error with $fieldName: " . $e->getMessage() . "<br>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h2>Current Table Structure:</h2>";
    
    $stmt = $db->query("DESCRIBE patient_outcomes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' cellpadding='8'>";
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
    
    echo "<hr>";
    echo "<p style='color: green; font-weight: bold;'>✅ All columns ready for outcome recording!</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
<style>
    body { font-family: Arial; margin: 20px; }
    h1, h2 { color: #333; }
    table { border-collapse: collapse; margin: 10px 0; background: white; }
    td { padding: 8px; border: 1px solid #ddd; }
    th { background: #f0f0f0; padding: 8px; text-align: left; }
</style>
