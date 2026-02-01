<?php
/**
 * RISK RESULT MODEL
 * ============================================================================
 * Handles risk assessment results and recommendations
 * 
 * NOTE: Phase 4 will implement the actual risk scoring logic here.
 * For now, this model stores and retrieves placeholder results.
 * ============================================================================
 */

class RiskResult {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Store risk assessment result
     * This will be called by the RiskEngine (Phase 4)
     */
    public function createRiskResult($assessment_id, $risk_score, $risk_level, $confidence, $primary_factors, $secondary_factors, $recommendation) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO risk_results (
                    assessment_id, risk_score, risk_level, confidence_percentage,
                    primary_factors, secondary_factors, clinical_recommendation
                ) VALUES (
                    :assessment_id, :risk_score, :risk_level, :confidence_percentage,
                    :primary_factors, :secondary_factors, :clinical_recommendation
                )
            ");
            
            $result = $stmt->execute([
                ':assessment_id' => $assessment_id,
                ':risk_score' => $risk_score,
                ':risk_level' => $risk_level,
                ':confidence_percentage' => $confidence,
                ':primary_factors' => $primary_factors,
                ':secondary_factors' => $secondary_factors,
                ':clinical_recommendation' => $recommendation
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Risk Result Creation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get risk result for an assessment
     */
    public function getRiskResultByAssessmentId($assessment_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM risk_results WHERE assessment_id = :assessment_id
            ");
            
            $stmt->execute([':assessment_id' => $assessment_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Risk Result Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create placeholder/dummy risk result for demo purposes
     * Replace this with Phase 4 actual scoring logic
     */
    public function createPlaceholderResult($assessment_id) {
        // TODO: PHASE 4 - Replace this with actual risk scoring engine
        // This function will be replaced by RiskEngine::calculateRisk()
        
        // For now, generate dummy results based on assessment
        $risk_score = rand(20, 85); // Random score 0-100
        $risk_level = 'Low';
        if ($risk_score >= 65) {
            $risk_level = 'High';
        } elseif ($risk_score >= 40) {
            $risk_level = 'Moderate';
        }
        
        $confidence = rand(70, 95); // Random confidence
        $primary_factors = "Smoking history, HPV status";
        $secondary_factors = "Alcohol consumption, Family history";
        $recommendation = "Schedule follow-up evaluation in 3 months.";
        
        return $this->createRiskResult(
            $assessment_id,
            $risk_score,
            $risk_level,
            $confidence,
            $primary_factors,
            $secondary_factors,
            $recommendation
        );
    }
    
    /**
     * Get risk statistics for admin dashboard
     */
    public function getRiskStatistics() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    risk_level,
                    COUNT(*) as count,
                    AVG(risk_score) as average_score,
                    MIN(risk_score) as min_score,
                    MAX(risk_score) as max_score
                FROM risk_results
                GROUP BY risk_level
            ");
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Risk Statistics Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get high-risk patients for admin monitoring
     */
    public function getHighRiskPatients($limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.id, p.first_name, p.last_name, rr.risk_score, 
                       rr.risk_level, rr.confidence_percentage, a.assessment_date
                FROM risk_results rr
                JOIN assessments a ON rr.assessment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                WHERE rr.risk_level = 'High'
                ORDER BY rr.risk_score DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get High Risk Patients Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
