<?php
/**
 * PATIENT OUTCOME MODEL
 * ============================================================================
 * Manages patient outcome tracking for population-level learning
 * Captures diagnosis, treatment, and follow-up data
 * ============================================================================
 */

class PatientOutcome {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Record patient outcome (diagnosis and treatment)
     */
    public function recordOutcome($patient_id, $assessment_id, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO patient_outcomes (
                    patient_id, assessment_id, final_diagnosis, cancer_stage,
                    cancer_type, treatment_type, outcome_date, follow_up_status,
                    survival_status, years_survived, notes
                ) VALUES (
                    :patient_id, :assessment_id, :final_diagnosis, :cancer_stage,
                    :cancer_type, :treatment_type, :outcome_date, :follow_up_status,
                    :survival_status, :years_survived, :notes
                )
            ");
            
            return $stmt->execute([
                ':patient_id' => $patient_id,
                ':assessment_id' => $assessment_id,
                ':final_diagnosis' => $data['final_diagnosis'] ?? null,
                ':cancer_stage' => $data['cancer_stage'] ?? null,
                ':cancer_type' => $data['cancer_type'] ?? null,
                ':treatment_type' => $data['treatment_type'] ?? null,
                ':outcome_date' => $data['outcome_date'] ?? date('Y-m-d'),
                ':follow_up_status' => $data['follow_up_status'] ?? null,
                ':survival_status' => $data['survival_status'] ?? 'Alive',
                ':years_survived' => $data['years_survived'] ?? null,
                ':notes' => $data['notes'] ?? null
            ]) ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Patient Outcome Recording Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get outcome for a patient's assessment
     */
    public function getOutcomeByAssessmentId($assessment_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM patient_outcomes WHERE assessment_id = :assessment_id
            ");
            
            $stmt->execute([':assessment_id' => $assessment_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Outcome Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all outcomes for a patient
     */
    public function getPatientOutcomes($patient_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT po.*, a.assessment_date, a.smoking_status, a.hpv_status,
                       rr.risk_score, rr.risk_level
                FROM patient_outcomes po
                JOIN assessments a ON po.assessment_id = a.id
                LEFT JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE po.patient_id = :patient_id
                ORDER BY a.assessment_date DESC
            ");
            
            $stmt->execute([':patient_id' => $patient_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Patient Outcomes Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update patient outcome
     */
    public function updateOutcome($outcome_id, $data) {
        try {
            $updates = [];
            $params = [':id' => $outcome_id];
            
            $allowedFields = ['final_diagnosis', 'cancer_stage', 'cancer_type', 'treatment_type',
                            'outcome_date', 'follow_up_status', 'survival_status', 'years_survived', 'notes'];
            
            foreach ($allowedFields as $field) {
                if (isset($data[$field])) {
                    $updates[] = "$field = :$field";
                    $params[":$field"] = $data[$field];
                }
            }
            
            if (empty($updates)) {
                return true;
            }
            
            $stmt = $this->db->prepare("UPDATE patient_outcomes SET " . implode(", ", $updates) . " WHERE id = :id");
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update Outcome Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get cohort statistics for similar patients
     * Find patients matching certain criteria and analyze their outcomes
     */
    public function getCohortStatistics($filters = []) {
        try {
            // Build query to find patients matching criteria
            $query = "
                SELECT 
                    COUNT(DISTINCT po.patient_id) as total_patients,
                    COUNT(DISTINCT CASE WHEN po.final_diagnosis = 'Malignant' THEN po.patient_id END) as malignancy_count,
                    COUNT(DISTINCT CASE WHEN po.final_diagnosis = 'Benign' THEN po.patient_id END) as benign_count,
                    COUNT(DISTINCT CASE WHEN po.survival_status = 'Alive' THEN po.patient_id END) as alive_count,
                    COUNT(DISTINCT CASE WHEN po.survival_status = 'Deceased' THEN po.patient_id END) as deceased_count,
                    AVG(rr.risk_score) as avg_risk_score,
                    AVG(YEAR(po.outcome_date) - YEAR(a.assessment_date)) as avg_years_to_diagnosis
                FROM patient_outcomes po
                JOIN assessments a ON po.assessment_id = a.id
                LEFT JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE 1=1
            ";
            
            $params = [];
            
            // Add filters if provided
            if (!empty($filters['hpv_status'])) {
                $query .= " AND a.hpv_status = :hpv_status";
                $params[':hpv_status'] = $filters['hpv_status'];
            }
            
            if (!empty($filters['age_min'])) {
                $query .= " AND YEAR(a.assessment_date) - YEAR(p.date_of_birth) >= :age_min";
                $params[':age_min'] = $filters['age_min'];
            }
            
            if (!empty($filters['symptom'])) {
                $query .= " AND a.{$filters['symptom']} = 1";
            }
            
            // Join patients table if age filtering
            if (!empty($filters['age_min']) || !empty($filters['age_max'])) {
                $query = str_replace(
                    "FROM patient_outcomes po",
                    "FROM patient_outcomes po JOIN patients p ON po.patient_id = p.id",
                    $query
                );
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Cohort Statistics Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Calculate malignancy rate for a patient cohort
     */
    public function getMalignancyRate($filters = []) {
        $stats = $this->getCohortStatistics($filters);
        
        if (!$stats || $stats['total_patients'] == 0) {
            return null;
        }
        
        return [
            'total_patients' => $stats['total_patients'],
            'malignancy_count' => $stats['malignancy_count'],
            'benign_count' => $stats['benign_count'],
            'malignancy_rate' => round(($stats['malignancy_count'] / $stats['total_patients']) * 100, 1),
            'benign_rate' => round(($stats['benign_count'] / $stats['total_patients']) * 100, 1),
            'survival_rate' => round(($stats['alive_count'] / $stats['total_patients']) * 100, 1),
            'avg_risk_score' => round($stats['avg_risk_score'] ?? 0, 1),
            'avg_years_to_diagnosis' => round($stats['avg_years_to_diagnosis'] ?? 0, 2)
        ];
    }
    
    /**
     * Get pattern analysis - find similar patients from history
     */
    public function findSimilarPatients($currentAssessment, $limit = 10) {
        try {
            // Build dynamic query to find similar patients
            $whereConditions = [];
            
            // Match on HPV status
            $hpv = $currentAssessment['hpv_status'] ?? 'unknown';
            if ($hpv !== 'unknown') {
                $whereConditions[] = "a.hpv_status = '{$hpv}'";
            }
            
            // Match on age (within 10 years)
            $currentAge = $currentAssessment['age'] ?? 50;
            $whereConditions[] = "YEAR(a.assessment_date) - YEAR(p.date_of_birth) BETWEEN " . 
                               ($currentAge - 10) . " AND " . ($currentAge + 10);
            
            // Match on key symptoms
            if (!empty($currentAssessment['sore_throat_duration']) && $currentAssessment['sore_throat_duration'] > 0) {
                $whereConditions[] = "a.sore_throat_duration > 0";
            }
            
            if (!empty($currentAssessment['voice_changes'])) {
                $whereConditions[] = "a.voice_changes = 1";
            }
            
            if (!empty($currentAssessment['difficulty_swallowing'])) {
                $whereConditions[] = "a.difficulty_swallowing = 1";
            }
            
            $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "WHERE 1=1";
            
            $query = "
                SELECT DISTINCT p.id, p.first_name, p.last_name, p.gender,
                       a.id as assessment_id, a.assessment_date, 
                       a.hpv_status, a.smoking_status,
                       rr.risk_score, rr.risk_level,
                       po.final_diagnosis, po.cancer_stage, po.treatment_type,
                       po.survival_status
                FROM patients p
                JOIN assessments a ON p.id = a.patient_id
                LEFT JOIN risk_results rr ON a.id = rr.assessment_id
                LEFT JOIN patient_outcomes po ON a.id = po.assessment_id
                {$whereClause}
                ORDER BY a.assessment_date DESC
                LIMIT :limit
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Find Similar Patients Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get outcome distribution for display purposes
     */
    public function getOutcomeDistribution($limit = 100) {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    final_diagnosis,
                    COUNT(*) as count,
                    ROUND(COUNT(*) * 100 / (SELECT COUNT(*) FROM patient_outcomes), 1) as percentage
                FROM patient_outcomes
                WHERE final_diagnosis IS NOT NULL
                GROUP BY final_diagnosis
                ORDER BY count DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Outcome Distribution Error: " . $e->getMessage());
            return [];
        }
    }
}
?>
