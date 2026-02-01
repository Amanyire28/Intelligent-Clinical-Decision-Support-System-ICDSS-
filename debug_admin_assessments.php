<?php
session_start();

echo "<h1>Debug: Admin Assessments</h1>";

// Check session
echo "<h2>Session Info:</h2>";
echo "<pre>";
echo "User ID: " . ($_SESSION['user_id'] ?? 'NOT SET') . "\n";
echo "User Role: " . ($_SESSION['user_role'] ?? 'NOT SET') . "\n";
echo "User Name: " . ($_SESSION['user_name'] ?? 'NOT SET') . "\n";
echo "Full Session:\n";
print_r($_SESSION);
echo "</pre>";

// If not authenticated, show message
if (!isset($_SESSION['user_id'])) {
    echo "<p><strong style='color: red;'>NOT AUTHENTICATED - You need to log in first!</strong></p>";
    echo "<a href='index.php?page=login'>Go to Login</a>";
    exit;
}

// If not admin, show message
if ($_SESSION['user_role'] !== 'admin') {
    echo "<p><strong style='color: red;'>NOT AUTHORIZED - Only admins can view assessments!</strong></p>";
    echo "Your role is: " . $_SESSION['user_role'];
    exit;
}

// Try to load the data
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Assessment.php';

$db = getDBConnection();
$assessmentModel = new Assessment($db);

$assessments = $assessmentModel->getAllAssessments(50, 0);

echo "<h2>Assessments Retrieved: " . count($assessments) . "</h2>";

if (count($assessments) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Patient</th><th>Doctor</th><th>Date</th><th>Risk Level</th><th>Score</th></tr>";
    foreach ($assessments as $a) {
        echo "<tr>";
        echo "<td>" . $a['assessment_id'] . "</td>";
        echo "<td>" . $a['first_name'] . " " . $a['last_name'] . "</td>";
        echo "<td>" . $a['doctor_name'] . "</td>";
        echo "<td>" . $a['assessment_date'] . "</td>";
        echo "<td>" . $a['risk_level'] . "</td>";
        echo "<td>" . $a['risk_score'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>No assessments found!</strong></p>";
}
?>
