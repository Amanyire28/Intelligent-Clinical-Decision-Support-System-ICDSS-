<?php
/**
 * ASSESSMENT MODEL
 * ============================================================================
 * Handles patient assessment (symptom & medical history collection)
 * ============================================================================
 */

class Assessment {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new assessment record
     * Stores all patient symptom and medical history data
     */
    public function createAssessment($patient_id, $doctor_id, $data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO assessments (
                    patient_id, doctor_id, smoking_status, smoking_years, 
                    alcohol_consumption, sore_throat_duration, voice_changes, 
                    difficulty_swallowing, neck_lump, weight_loss, 
                    weight_loss_percentage, unexplained_weight_loss_weeks,
                    hpv_status, hpv_test_date, family_cancer_history, 
                    family_cancer_type, hemoglobin_level, lymphocyte_count,
                    clinical_notes
                ) VALUES (
                    :patient_id, :doctor_id, :smoking_status, :smoking_years,
                    :alcohol_consumption, :sore_throat_duration, :voice_changes,
                    :difficulty_swallowing, :neck_lump, :weight_loss,
                    :weight_loss_percentage, :unexplained_weight_loss_weeks,
                    :hpv_status, :hpv_test_date, :family_cancer_history,
                    :family_cancer_type, :hemoglobin_level, :lymphocyte_count,
                    :clinical_notes
                )
            ");
            
            $result = $stmt->execute([
                ':patient_id' => $patient_id,
                ':doctor_id' => $doctor_id,
                ':smoking_status' => $data['smoking_status'] ?? 'never',
                ':smoking_years' => $data['smoking_years'] ?? 0,
                ':alcohol_consumption' => $data['alcohol_consumption'] ?? 'none',
                ':sore_throat_duration' => $data['sore_throat_duration'] ?? 0,
                ':voice_changes' => $data['voice_changes'] ?? false,
                ':difficulty_swallowing' => $data['difficulty_swallowing'] ?? false,
                ':neck_lump' => $data['neck_lump'] ?? false,
                ':weight_loss' => $data['weight_loss'] ?? false,
                ':weight_loss_percentage' => $data['weight_loss_percentage'] ?? 0,
                ':unexplained_weight_loss_weeks' => $data['unexplained_weight_loss_weeks'] ?? 0,
                ':hpv_status' => $data['hpv_status'] ?? 'unknown',
                ':hpv_test_date' => $data['hpv_test_date'] ?? null,
                ':family_cancer_history' => $data['family_cancer_history'] ?? false,
                ':family_cancer_type' => $data['family_cancer_type'] ?? null,
                ':hemoglobin_level' => $data['hemoglobin_level'] ?? null,
                ':lymphocyte_count' => $data['lymphocyte_count'] ?? null,
                ':clinical_notes' => $data['clinical_notes'] ?? null
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Assessment Creation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get assessment by ID with full details
     */
    public function getAssessmentById($assessment_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.*, p.first_name, p.last_name, p.date_of_birth, p.gender,
                       u.full_name as doctor_name
                FROM assessments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON a.doctor_id = u.id
                WHERE a.id = :id
            ");
            
            $stmt->execute([':id' => $assessment_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Assessment Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get all assessments for a specific patient
     */
    public function getPatientAssessments($patient_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.id, a.assessment_date, a.smoking_status, a.hpv_status,
                       u.full_name as doctor_name, 
                       rr.risk_level, rr.risk_score
                FROM assessments a
                JOIN users u ON a.doctor_id = u.id
                LEFT JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE a.patient_id = :patient_id
                ORDER BY a.assessment_date DESC
            ");
            
            $stmt->execute([':patient_id' => $patient_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Patient Assessments Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recent assessments for a specific doctor
     */
    public function getDoctorRecentAssessments($doctor_id, $limit = 10) {
        try {
            $stmt = $this->db->prepare("
                SELECT a.id, a.assessment_date, 
                       p.first_name, p.last_name,
                       rr.risk_level, rr.risk_score
                FROM assessments a
                JOIN patients p ON a.patient_id = p.id
                LEFT JOIN risk_results rr ON a.id = rr.assessment_id
                WHERE a.doctor_id = :doctor_id
                ORDER BY a.assessment_date DESC
                LIMIT :limit
            ");
            
            $stmt->bindValue(':doctor_id', $doctor_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Doctor Recent Assessments Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get all assessments (for admin dashboard)
     */
    public function getAllAssessments($limit = 100, $offset = 0) {
        try {
            $stmt = $this->db->prepare("
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
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get All Assessments Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get total count of assessments
     */
    public function getTotalAssessmentCount() {
        try {
            $stmt = $this->db->query("SELECT COUNT(*) as count FROM assessments");
            $result = $stmt->fetch();
            return $result['count'] ?? 0;
        } catch (PDOException $e) {
            error_log("Count Assessments Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Update assessment (for editing assessments before final submission)
     */
    public function updateAssessment($assessment_id, $data) {
        try {
            // Build dynamic update query based on provided fields
            $updates = [];
            $params = [':id' => $assessment_id];
            
            foreach ($data as $key => $value) {
                if (in_array($key, ['smoking_status', 'smoking_years', 'alcohol_consumption', 'sore_throat_duration', 'voice_changes', 'difficulty_swallowing', 'neck_lump', 'weight_loss', 'weight_loss_percentage', 'unexplained_weight_loss_weeks', 'hpv_status', 'family_cancer_history', 'family_cancer_type', 'hemoglobin_level', 'lymphocyte_count', 'clinical_notes'])) {
                    $updates[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (empty($updates)) {
                return true;
            }
            
            $stmt = $this->db->prepare("UPDATE assessments SET " . implode(", ", $updates) . " WHERE id = :id");
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Assessment Update Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
