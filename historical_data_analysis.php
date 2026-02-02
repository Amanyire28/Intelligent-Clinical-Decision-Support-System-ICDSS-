<?php
/**
 * HISTORICAL DATA ANALYSIS CAPABILITY CHECK
 * Shows what the system currently does with past outcomes
 */

echo "<h1>System Capability Analysis: Historical Data Usage</h1>";
echo "<hr>";

$capabilities = [
    'ONE-WAY SYSTEM (Current)' => [
        'Assessment Creation' => ['status' => '✅', 'desc' => 'Doctor enters patient data and symptoms'],
        'Risk Calculation' => ['status' => '✅', 'desc' => 'System calculates risk based on current data only'],
        'Outcome Recording' => ['status' => '✅', 'desc' => 'Doctor records what diagnosis was given'],
        'Data Storage' => ['status' => '✅', 'desc' => 'All outcomes stored in database'],
    ],
    
    'WHAT IT DOESN\'T DO YET (Future Enhancement)' => [
        'Historical Pattern Recognition' => ['status' => '❌', 'desc' => 'Cannot find similar past cases to compare against'],
        'Outcome-Based Recommendations' => ['status' => '❌', 'desc' => 'Cannot suggest treatment based on what worked before'],
        'Cohort Analysis' => ['status' => '❌', 'desc' => 'Cannot show survival rates for similar patients'],
        'Treatment Success Tracking' => ['status' => '❌', 'desc' => 'Cannot analyze which treatments worked best'],
        'Risk Model Improvement' => ['status' => '❌', 'desc' => 'Risk algorithm doesn\'t learn from outcomes'],
        'Decision Support from History' => ['status' => '❌', 'desc' => 'Cannot recommend action based on similar past cases'],
    ],
    
    'DATA CURRENTLY STORED' => [
        'Patient Demographics' => ['status' => '✅', 'desc' => 'Age, gender, contact info, medical history'],
        'Risk Factors' => ['status' => '✅', 'desc' => 'Smoking, symptoms, lab values at time of assessment'],
        'Risk Scores' => ['status' => '✅', 'desc' => 'Risk level and confidence for each assessment'],
        'Outcomes' => ['status' => '✅', 'desc' => 'Final diagnosis, treatment given, recommendations'],
        'Clinical Notes' => ['status' => '✅', 'desc' => 'Doctor findings and treatment reasoning'],
    ],
];

foreach ($capabilities as $category => $items) {
    echo "<h2>$category</h2>";
    echo "<table border='1' cellpadding='12' style='width: 100%; margin: 10px 0;'>";
    echo "<tr><th style='width: 30%;'>Feature</th><th style='width: 70%;'>Description</th></tr>";
    foreach ($items as $feature => $data) {
        echo "<tr>";
        echo "<td><strong>" . $data['status'] . " " . $feature . "</strong></td>";
        echo "<td>" . $data['desc'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<br>";
}

echo "<hr>";
echo "<h2>Example Scenario:</h2>";
echo "<div style='background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<p><strong>Current System (One-way):</strong></p>";
echo "<ol>";
echo "<li>Patient A with smoking history + sore throat → Risk Score: 65 (Moderate)</li>";
echo "<li>Doctor records: Diagnosis = Benign, Treatment = Monitoring</li>";
echo "<li>6 months later: Patient B with SAME risk factors → Risk Score: 65 (Moderate)</li>";
echo "<li>❌ System does NOT tell doctor: 'Similar patient A turned out to be benign'</li>";
echo "<li>❌ System does NOT compare outcomes between Patient A and B</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #e8f5e9; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
echo "<p><strong>What We Could Add (Phase 2):</strong></p>";
echo "<ol>";
echo "<li>Patient B assessment → Risk Score: 65 (Moderate)</li>";
echo "<li>System searches historical data: 'Found 7 similar cases'</li>";
echo "<li>System shows: '6 of 7 similar patients had benign diagnoses'</li>";
echo "<li>System recommends: 'Close monitoring recommended based on similar cases'</li>";
echo "<li>Doctor can see: outcomes of similar historical cases side-by-side</li>";
</ol>";
echo "</div>";

echo "<h2>Data Available for Historical Analysis:</h2>";
require_once __DIR__ . '/config/db_config.php';
try {
    $db = getDBConnection();
    
    // Count available historical data
    $stats = [];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM assessments");
    $stats['Total Assessments'] = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM patient_outcomes");
    $stats['Recorded Outcomes'] = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(DISTINCT patient_id) as count FROM assessments");
    $stats['Unique Patients'] = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM patient_outcomes WHERE final_diagnosis = 'Malignant'");
    $stats['Malignant Cases'] = $stmt->fetch()['count'];
    
    $stmt = $db->query("SELECT COUNT(*) as count FROM patient_outcomes WHERE final_diagnosis = 'Benign'");
    $stats['Benign Cases'] = $stmt->fetch()['count'];
    
    echo "<table border='1' cellpadding='10' style='margin: 10px 0;'>";
    echo "<tr><th>Metric</th><th>Count</th></tr>";
    foreach ($stats as $key => $value) {
        echo "<tr><td><strong>$key</strong></td><td><strong>$value</strong></td></tr>";
    }
    echo "</table>";
    
    if ($stats['Recorded Outcomes'] > 0) {
        echo "<p style='color: green;'>✅ You have historical outcome data that could power decision support!</p>";
    }
} catch (Exception $e) {
    echo "<p>Could not retrieve data: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<h2>What Would Phase 2 Look Like?</h2>";
echo "<div style='background: #e3f2fd; padding: 15px; border-radius: 5px;'>";
echo "<p><strong>Historical Data Integration:</strong></p>";
echo "<ul>";
echo "<li>🔍 <strong>Similar Case Finder:</strong> Find patients with matching risk factors and see their outcomes</li>";
echo "<li>📊 <strong>Cohort Analytics:</strong> Show survival rates and treatment success for similar cohorts</li>";
echo "<li>💡 <strong>Decision Support:</strong> Suggest actions based on what worked for similar historical cases</li>";
echo "<li>📈 <strong>Trend Analysis:</strong> Track how effective each treatment is over time</li>";
echo "<li>🎯 <strong>Risk Model Refinement:</strong> Improve the risk algorithm based on real outcomes</li>";
echo "</ul>";
echo "</div>";

?>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 20px;
        background: #f5f5f5;
    }
    h1, h2 {
        color: #333;
    }
    table {
        border-collapse: collapse;
        background: white;
    }
    th {
        background: #f0f0f0;
        padding: 10px;
        text-align: left;
    }
    td {
        padding: 10px;
    }
</style>
