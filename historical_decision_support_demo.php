<?php
/**
 * Historical Data Analysis - System Capabilities Demonstration
 */
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/HistoricalAnalytics.php';

$db = getDBConnection();
$analytics = new HistoricalAnalytics($db);

echo "<h1>📊 Historical Decision Support System - Live Demo</h1>";
echo "<hr>";

// Get a sample assessment with risk score
$stmt = $db->prepare("
    SELECT a.*, rr.risk_score, rr.risk_level, p.date_of_birth
    FROM assessments a
    LEFT JOIN risk_results rr ON a.id = rr.assessment_id
    LEFT JOIN patients p ON a.patient_id = p.id
    WHERE rr.risk_level IS NOT NULL
    LIMIT 1
");
$stmt->execute();
$assessment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$assessment) {
    echo "<p style='color: orange;'>⚠️ No assessments with risk scores found. Please create an assessment first.</p>";
    exit;
}

echo "<h2>Sample Assessment: ID #" . $assessment['id'] . "</h2>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><td><strong>Smoking Status:</strong></td><td>" . $assessment['smoking_status'] . "</td></tr>";
echo "<tr><td><strong>Risk Level:</strong></td><td style='color: " . 
    ($assessment['risk_level'] === 'High' ? '#d32f2f' : ($assessment['risk_level'] === 'Moderate' ? '#ff9800' : '#4caf50')) . 
    "; font-weight: bold;'>" . $assessment['risk_level'] . "</td></tr>";
echo "<tr><td><strong>Risk Score:</strong></td><td>" . $assessment['risk_score'] . "/100</td></tr>";
echo "</table>";

echo "<h2>1️⃣ Similar Cases Finder</h2>";
$similarCases = $analytics->findSimilarCases($assessment, 5);
if (count($similarCases) > 0) {
    echo "<p>Found <strong>" . count($similarCases) . "</strong> similar historical cases:</p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Date</th><th>Risk Level</th><th>Score</th><th>Diagnosis</th><th>Treatment</th><th>Status</th></tr>";
    foreach ($similarCases as $case) {
        $diagColor = $case['final_diagnosis'] === 'Malignant' ? '#d32f2f' : '#4caf50';
        echo "<tr>";
        echo "<td>" . substr($case['assessment_date'], 0, 10) . "</td>";
        echo "<td>" . $case['risk_level'] . "</td>";
        echo "<td>" . $case['risk_score'] . "/100</td>";
        echo "<td style='color: $diagColor; font-weight: bold;'>" . ($case['final_diagnosis'] ?? 'Unknown') . "</td>";
        echo "<td>" . ($case['treatment_plan'] ?? '—') . "</td>";
        echo "<td>" . ($case['follow_up_status'] ?? '—') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No similar cases found yet.</p>";
}

echo "<h2>2️⃣ Cohort Statistics</h2>";
$cohortStats = $analytics->getCohortStats($assessment['smoking_status'], $assessment['risk_level']);
if ($cohortStats && $cohortStats['total_patients'] > 0) {
    echo "<p>Analysis of <strong>" . $cohortStats['total_patients'] . "</strong> similar patients:</p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><td><strong>Total Patients:</strong></td><td>" . $cohortStats['total_patients'] . "</td></tr>";
    echo "<tr><td><strong>Malignant Cases:</strong></td><td>" . $cohortStats['malignant_count'] . " (" . $cohortStats['malignancy_rate'] . "%)</td></tr>";
    echo "<tr><td><strong>Average Risk Score:</strong></td><td>" . $cohortStats['avg_risk_score'] . "/100</td></tr>";
    echo "<tr><td><strong>Average Days to Diagnosis:</strong></td><td>" . round($cohortStats['avg_days_to_diagnosis']) . " days</td></tr>";
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Not enough similar cases in database.</p>";
}

echo "<h2>3️⃣ Risk Accuracy Analysis</h2>";
$riskAccuracy = $analytics->getRiskAccuracy($assessment['risk_level']);
if ($riskAccuracy) {
    echo "<p>Historical accuracy for <strong>" . $assessment['risk_level'] . " Risk</strong> predictions:</p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><td><strong>Total Historical Cases:</strong></td><td>" . $riskAccuracy['total_cases'] . "</td></tr>";
    echo "<tr><td><strong>Actual Malignant Cases:</strong></td><td style='color: #d32f2f; font-weight: bold;'>" . $riskAccuracy['actual_malignant'] . "</td></tr>";
    echo "<tr><td><strong>Actual Malignancy Rate:</strong></td><td style='color: #d32f2f; font-weight: bold;'>" . $riskAccuracy['actual_malignancy_rate'] . "%</td></tr>";
    echo "<tr><td><strong>Expected Outcome:</strong></td><td>" . $riskAccuracy['expected_outcome'] . "</td></tr>";
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ Not enough data for accuracy analysis.</p>";
}

echo "<h2>4️⃣ Diagnosis Distribution</h2>";
$diagnosis = $analytics->getDiagnosisDistribution($assessment['risk_level'], $assessment['smoking_status']);
if (count($diagnosis) > 0) {
    echo "<p>How similar cases were diagnosed:</p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Diagnosis</th><th>Count</th><th>Percentage</th></tr>";
    foreach ($diagnosis as $d) {
        echo "<tr>";
        echo "<td style='color: " . ($d['final_diagnosis'] === 'Malignant' ? '#d32f2f' : '#4caf50') . "; font-weight: bold;'>" . $d['final_diagnosis'] . "</td>";
        echo "<td>" . $d['count'] . "</td>";
        echo "<td>" . $d['percentage'] . "%</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: orange;'>⚠️ No diagnosis data available.</p>";
}

echo "<h2>5️⃣ Automated Recommendation</h2>";
$recommendation = $analytics->generateRecommendation($assessment['risk_level'], $assessment['smoking_status'], '');
if ($recommendation) {
    $confColor = $recommendation['confidence'] === 'high' ? '#4caf50' : '#ff9800';
    echo "<div style='background: ${confColor}22; border-left: 4px solid $confColor; padding: 15px; border-radius: 5px;'>";
    echo "<p style='color: $confColor; font-weight: bold;'>Confidence: " . strtoupper($recommendation['confidence']) . "</p>";
    echo "<p>" . $recommendation['message'] . "</p>";
    echo "<p style='font-size: 12px; color: #666;'>Based on " . $recommendation['data_points'] . " similar historical cases</p>";
    echo "</div>";
} else {
    echo "<p style='color: orange;'>⚠️ Not enough historical data for recommendation.</p>";
}

echo "<hr>";
echo "<h2>✅ Historical Decision Support Features Implemented:</h2>";
echo "<ul>";
echo "<li><strong>Similar Case Finder:</strong> Identifies patients with matching risk profiles and shows their outcomes</li>";
echo "<li><strong>Cohort Analytics:</strong> Shows malignancy rates, average risk scores, and diagnostic timelines for similar patients</li>";
echo "<li><strong>Risk Assessment Validation:</strong> Compares predicted risk levels against actual historical outcomes</li>";
echo "<li><strong>Treatment Outcome Tracking:</strong> (Ready to implement - need more treatment follow-up data)</li>";
echo "<li><strong>Automated Recommendations:</strong> Generates evidence-based recommendations from similar historical cases</li>";
echo "</ul>";

echo "<p style='margin-top: 20px; padding: 15px; background: #e8f5e9; border-radius: 5px;'>";
echo "💡 <strong>How It Works:</strong> When a doctor views an assessment, the system automatically searches historical data for similar cases, ";
echo "analyzes outcomes, and provides evidence-based recommendations to support clinical decision-making.";
echo "</p>";

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
        margin: 10px 0;
    }
    th, td {
        padding: 10px;
    }
    th {
        background: #f0f0f0;
        text-align: left;
    }
    hr {
        border: none;
        border-top: 2px solid #ccc;
        margin: 20px 0;
    }
</style>
