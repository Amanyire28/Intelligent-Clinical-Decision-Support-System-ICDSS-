<?php
require_once __DIR__ . '/config/db_config.php';

$db = getDBConnection();

// Get table structure
$stmt = $db->query("DESCRIBE patient_outcomes");
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h1>patient_outcomes Table Structure</h1>";
echo "<table border='1' cellpadding='10'>";
echo "<tr>";
echo "<th>Field</th>";
echo "<th>Type</th>";
echo "<th>Null</th>";
echo "<th>Key</th>";
echo "<th>Default</th>";
echo "<th>Extra</th>";
echo "</tr>";

foreach ($columns as $col) {
    echo "<tr>";
    echo "<td><strong>" . htmlspecialchars($col['Field']) . "</strong></td>";
    echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
    echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
    echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
    echo "<td>" . htmlspecialchars($col['Default'] ?? '(no default)') . "</td>";
    echo "<td>" . htmlspecialchars($col['Extra'] ?? '') . "</td>";
    echo "</tr>";
}
echo "</table>";

echo "<h2>Expected Fields Check:</h2>";
$expected = [
    'treatment_plan',
    'treatment_urgency',
    'clinical_findings',
    'recommendations',
    'follow_up_date',
    'tumor_location',
    'recorded_at'
];

$found = array_column($columns, 'Field');
foreach ($expected as $field) {
    if (in_array($field, $found)) {
        echo "✅ $field<br>";
    } else {
        echo "❌ $field (MISSING)<br>";
    }
}
?>
