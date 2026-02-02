<?php
/**
 * HISTORICAL ANALYTICS MODEL
 * ============================================================================
 * Analyzes historical outcomes to provide decision support
 * Finds similar cases, tracks treatment success, and generates insights
 * ============================================================================
 */

class HistoricalAnalytics {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Find similar cases based on risk factors
     * Returns cases with similar demographics and symptoms
     */
    public function findSimilarCases($assessment, $limit = 5) {
        try {
            // Match on: smoking status, symptoms, and similar age range
            $stmt = $this->db->prepare("
                SELECT 
                    a.id as assessment_id,
                    a.assessment_date,
                    p.first_name,
                    p.last_name,
                    YEAR(FROM_DAYS(DATEDIFF(NOW(), p.date_of_birth))) as age,
                    a.smoking_status,
                    a.sore_throat_duration,
                    a.voice_changes,
                    a.difficulty_swallowing,
                    a.neck_lump,
                    rr.risk_level,
                    rr.risk_score,
                    po.final_diagnosis,
                    po.treatment_plan,
                    po.follow_up_status,
                    DATEDIFF(po.outcome_date, a.assessment_date) as days_to_diagnosis
                FROM assessments a
                LEFT JOIN patients p ON a.patient_id = p.id
                LEFT JOIN risk_results rr ON a.id = rr.assessment_id
                LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
                WHERE 
                    a.smoking_status = ? 
                    AND a.id != ?
                    AND po.id IS NOT NULL
                    AND ABS(YEAR(FROM_DAYS(DATEDIFF(NOW(), p.date_of_birth))) - ?) <= 10
                ORDER BY 
                    ABS(rr.risk_score - ?) ASC,
                    a.assessment_date DESC
                LIMIT ?
            ");
            
            $age = intval((time() - strtotime($assessment['date_of_birth'])) / (365.25 * 86400));
            
            $stmt->execute([
                $assessment['smoking_status'] ?? 'unknown',
                $assessment['id'],
                $age,
                $assessment['risk_score'] ?? 50,
                $limit
            ]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Similar Cases Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get diagnosis breakdown for similar risk profile
     */
    public function getDiagnosisDistribution($riskLevel, $smokingStatus) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    po.final_diagnosis,
                    COUNT(*) as count,
                    ROUND(COUNT(*) * 100 / (SELECT COUNT(*) FROM patient_outcomes po2 
                        JOIN risk_results rr2 ON po2.assessment_id = rr2.assessment_id
                        WHERE rr2.risk_level = ? AND po2.final_diagnosis IS NOT NULL), 1) as percentage
                FROM patient_outcomes po
                JOIN assessments a ON po.assessment_id = a.id
                JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE rr.risk_level = ? AND po.final_diagnosis IS NOT NULL
                GROUP BY po.final_diagnosis
                ORDER BY count DESC
            ");
            
            $stmt->execute([$riskLevel, $riskLevel]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Diagnosis Distribution Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get treatment effectiveness for specific diagnosis
     */
    public function getTreatmentEffectiveness($diagnosis) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    po.treatment_plan,
                    COUNT(*) as total_cases,
                    SUM(CASE WHEN po.follow_up_status = 'NED' THEN 1 ELSE 0 END) as successful,
                    ROUND(SUM(CASE WHEN po.follow_up_status = 'NED' THEN 1 ELSE 0 END) * 100 / COUNT(*), 1) as success_rate
                FROM patient_outcomes po
                WHERE po.final_diagnosis = ? AND po.treatment_plan IS NOT NULL
                GROUP BY po.treatment_plan
                ORDER BY success_rate DESC
            ");
            
            $stmt->execute([$diagnosis]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Treatment Effectiveness Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get cohort statistics for similar patients
     */
    public function getCohortStats($smokingStatus, $riskLevel) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(DISTINCT po.patient_id) as total_patients,
                    COUNT(DISTINCT CASE WHEN po.final_diagnosis = 'Malignant' THEN po.patient_id END) as malignant_count,
                    ROUND(COUNT(DISTINCT CASE WHEN po.final_diagnosis = 'Malignant' THEN po.patient_id END) * 100 / 
                        COUNT(DISTINCT po.patient_id), 1) as malignancy_rate,
                    ROUND(AVG(rr.risk_score), 1) as avg_risk_score,
                    AVG(DATEDIFF(po.outcome_date, a.assessment_date)) as avg_days_to_diagnosis
                FROM patient_outcomes po
                JOIN assessments a ON po.assessment_id = a.id
                JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE rr.risk_level = ? AND po.final_diagnosis IS NOT NULL
            ");
            
            $stmt->execute([$riskLevel]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Cohort Stats Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get risk score accuracy - how well predictions match actual outcomes
     */
    public function getRiskAccuracy($riskLevel) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    rr.risk_level,
                    COUNT(*) as total_cases,
                    SUM(CASE WHEN po.final_diagnosis = 'Malignant' THEN 1 ELSE 0 END) as actual_malignant,
                    ROUND(SUM(CASE WHEN po.final_diagnosis = 'Malignant' THEN 1 ELSE 0 END) * 100 / COUNT(*), 1) as actual_malignancy_rate,
                    CASE 
                        WHEN rr.risk_level = 'High' THEN 'Expected: High malignancy'
                        WHEN rr.risk_level = 'Moderate' THEN 'Expected: Moderate malignancy'
                        WHEN rr.risk_level = 'Low' THEN 'Expected: Low malignancy'
                    END as expected_outcome
                FROM patient_outcomes po
                JOIN assessments a ON po.assessment_id = a.id
                JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE rr.risk_level = ? AND po.final_diagnosis IS NOT NULL
                GROUP BY rr.risk_level
            ");
            
            $stmt->execute([$riskLevel]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Risk Accuracy Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Generate recommendation based on historical data
     */
    public function generateRecommendation($riskLevel, $smokingStatus, $symptoms) {
        try {
            // Get similar cases
            $cohortStats = $this->getCohortStats($smokingStatus, $riskLevel);
            $riskAccuracy = $this->getRiskAccuracy($riskLevel);
            
            if (!$cohortStats || $cohortStats['total_patients'] < 3) {
                return null; // Not enough data
            }
            
            $recommendation = [
                'confidence' => 'moderate',
                'data_points' => intval($cohortStats['total_patients']),
                'message' => '',
                'details' => $cohortStats
            ];
            
            // Generate message based on risk level and historical outcomes
            if ($riskLevel === 'High') {
                if ($cohortStats['malignancy_rate'] > 60) {
                    $recommendation['message'] = 'Historical data shows ' . $cohortStats['malignancy_rate'] . 
                        '% of similar high-risk cases were malignant. Strong recommendation for aggressive follow-up.';
                    $recommendation['confidence'] = 'high';
                } else if ($cohortStats['malignancy_rate'] > 30) {
                    $recommendation['message'] = 'Significant malignancy rate (' . $cohortStats['malignancy_rate'] . 
                        '%) in similar cases. Close monitoring essential.';
                    $recommendation['confidence'] = 'high';
                }
            } else if ($riskLevel === 'Moderate') {
                if ($cohortStats['malignancy_rate'] < 20) {
                    $recommendation['message'] = 'Historical data suggests mostly benign outcomes (' . (100 - $cohortStats['malignancy_rate']) . 
                        '%) for similar moderate-risk cases.';
                    $recommendation['confidence'] = 'moderate';
                }
            } else if ($riskLevel === 'Low') {
                if ($cohortStats['malignancy_rate'] < 5) {
                    $recommendation['message'] = 'Historical data shows very low malignancy rate (' . $cohortStats['malignancy_rate'] . 
                        '%) for similar low-risk cases. Routine monitoring likely sufficient.';
                    $recommendation['confidence'] = 'high';
                }
            }
            
            return $recommendation;
        } catch (Exception $e) {
            error_log("Recommendation Generation Error: " . $e->getMessage());
            return null;
        }
    }
}
?>
