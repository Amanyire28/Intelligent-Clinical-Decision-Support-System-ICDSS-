<?php
/**
 * Test Historical Insights API
 */
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'doctor';

require_once __DIR__ . '/config/db_config.php';

echo "<h1>Testing Historical Insights API</h1>";
echo "<hr>";

try {
    $db = getDBConnection();
    
    // Find an assessment with risk score
    $stmt = $db->query("
        SELECT a.id FROM assessments a
        LEFT JOIN risk_results rr ON a.id = rr.assessment_id
        WHERE rr.risk_level IS NOT NULL
        LIMIT 1
    ");
    $assessment = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$assessment) {
        echo "<p style='color: orange;'>⚠️ No assessments with risk scores found.</p>";
        exit;
    }
    
    echo "<p>Testing with Assessment ID: " . $assessment['id'] . "</p>";
    echo "<hr>";
    
    // Test the API endpoint
    $apiUrl = "http://localhost/CANCER/index.php?page=api-historical-insights&action=full-insights&assessment_id=" . $assessment['id'];
    echo "<p>API URL: <code>" . $apiUrl . "</code></p>";
    
    $response = file_get_contents($apiUrl);
    $data = json_decode($response, true);
    
    echo "<h2>API Response:</h2>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
    echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    echo "</pre>";
    
    if ($data['success']) {
        echo "<h2>✅ API Working!</h2>";
        echo "<p>Retrieved data sections:</p>";
        echo "<ul>";
        foreach (array_keys($data['data']) as $key) {
            $count = 0;
            if (is_array($data['data'][$key])) {
                $count = count($data['data'][$key]);
            }
            echo "<li><strong>" . $key . "</strong> - " . ($count > 0 ? $count . " items" : "1 object") . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<h2>❌ API Error:</h2>";
        echo "<p>" . $data['message'] . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>
<style>
    body { font-family: Arial; margin: 20px; }
    h1, h2 { color: #333; }
    code { background: #eee; padding: 2px 5px; border-radius: 3px; }
    hr { margin: 20px 0; border: none; border-top: 2px solid #ccc; }
</style>
