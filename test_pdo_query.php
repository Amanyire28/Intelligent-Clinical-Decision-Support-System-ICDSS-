<?php
// Test PDO query directly
try {
    $pdo = new PDO(
        "mysql:host=localhost;port=3306;dbname=icdss_cancer_db;charset=utf8mb4",
        "root",
        "",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
    
    // Test 1: Simple count
    $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM assessments");
    $count = $stmt->fetch();
    echo "Total Assessments: " . $count['cnt'] . "\n\n";
    
    // Test 2: Get all assessments with the exact query from the model
    $stmt = $pdo->prepare("
        SELECT a.id as assessment_id, a.assessment_date, 
               p.first_name, p.last_name,
               u.full_name as doctor_name,
               rr.risk_level, rr.risk_score, rr.confidence_percentage
        FROM assessments a
        JOIN patients p ON a.patient_id = p.id
        JOIN users u ON a.doctor_id = u.id
        LEFT JOIN risk_results rr ON a.id = rr.assessment_id
        ORDER BY a.assessment_date DESC
        LIMIT :limit OFFSET :offset
    ");
    
    $stmt->bindValue(':limit', 50, PDO::PARAM_INT);
    $stmt->bindValue(':offset', 0, PDO::PARAM_INT);
    $stmt->execute();
    $assessments = $stmt->fetchAll();
    
    echo "Assessments returned by model query: " . count($assessments) . "\n\n";
    
    if (count($assessments) > 0) {
        echo "First record:\n";
        print_r($assessments[0]);
    } else {
        echo "NO RECORDS RETURNED!\n";
    }
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
