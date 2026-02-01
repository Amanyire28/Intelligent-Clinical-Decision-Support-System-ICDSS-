<?php
// Simulate the controller call with proper variables
session_start();

// Set up admin session
$_SESSION['user_id'] = 2;
$_SESSION['user_name'] = 'System Administrator';
$_SESSION['user_role'] = 'admin';

// Simulate what the controller does
require_once __DIR__ . '/config/db_config.php';
require_once __DIR__ . '/models/Assessment.php';
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/RiskResult.php';

$db = getDBConnection();
$assessmentModel = new Assessment($db);
$userModel = new User($db);
$riskResultModel = new RiskResult($db);

// This is what the controller does
$current_user = [
    'id' => $_SESSION['user_id'],
    'full_name' => $_SESSION['user_name']
];

$page = 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$assessments = $assessmentModel->getAllAssessments($limit, $offset);
$total_count = $assessmentModel->getTotalAssessmentCount();
$total_pages = ceil($total_count / $limit);

// Now output what we have before including the view
echo "<h2>Before view inclusion:</h2>";
echo "<p>isset(\$assessments) = " . (isset($assessments) ? 'true' : 'false') . "</p>";
echo "<p>!empty(\$assessments) = " . (!empty($assessments) ? 'true' : 'false') . "</p>";
echo "<p>count(\$assessments) = " . count($assessments) . "</p>";

// Now include the view
ob_start();
include __DIR__ . '/views/admin_assessments.php';
$output = ob_get_clean();

// Look for the debug comment
if (strpos($output, 'DEBUG:') !== false) {
    echo "<h2>Found DEBUG comment!</h2>";
    preg_match('/<!-- DEBUG:.*?-->/s', $output, $matches);
    if (!empty($matches)) {
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
    }
} else {
    echo "<h2>NO DEBUG comment found in output</h2>";
}

// Look for "No assessments"
if (strpos($output, 'No assessments') !== false) {
    echo "<p style='color: red;'><strong>FOUND: 'No assessments found' in the view output</strong></p>";
} else {
    echo "<p style='color: green;'><strong>NOT FOUND: 'No assessments' is NOT in the view output</strong></p>";
    if (strpos($output, 'angel CASTRAL') !== false) {
        echo "<p style='color: green;'><strong>FOUND: Assessment data IS displayed!</strong></p>";
    }
}

// Show first 3000 chars around assessment table
echo "<h2>Table content (snippet):</h2>";
$pos = strpos($output, '<tbody>');
if ($pos !== false) {
    $snippet = substr($output, $pos, 2000);
    echo "<pre>" . htmlspecialchars($snippet) . "</pre>";
}
?>
