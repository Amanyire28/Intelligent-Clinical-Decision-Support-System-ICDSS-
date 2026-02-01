<?php
/**
 * PATIENT MODEL
 * ============================================================================
 * Handles patient-related database operations
 * ============================================================================
 */

class Patient {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    /**
     * Create new patient record
     */
    public function createPatient($first_name, $last_name, $date_of_birth, $gender, $contact_phone = null, $contact_email = null, $mrn = null) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO patients (first_name, last_name, date_of_birth, gender, contact_phone, contact_email, medical_record_number)
                VALUES (:first_name, :last_name, :date_of_birth, :gender, :contact_phone, :contact_email, :mrn)
            ");
            
            $result = $stmt->execute([
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':date_of_birth' => $date_of_birth,
                ':gender' => $gender,
                ':contact_phone' => $contact_phone,
                ':contact_email' => $contact_email,
                ':mrn' => $mrn
            ]);
            
            return $result ? $this->db->lastInsertId() : false;
        } catch (PDOException $e) {
            error_log("Patient Creation Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get patient by ID
     */
    public function getPatientById($patient_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM patients WHERE id = :id
            ");
            
            $stmt->execute([':id' => $patient_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Patient Error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Search patients by name or medical record number
     */
    public function searchPatients($search_term) {
        try {
            $search = "%" . $search_term . "%";
            error_log("=== PATIENT SEARCH DEBUG ===");
            error_log("Search term: '$search_term'");
            error_log("Search pattern: '$search'");
            
            $stmt = $this->db->prepare("
                SELECT id, first_name, last_name, date_of_birth, gender, medical_record_number, created_at
                FROM patients 
                WHERE first_name LIKE :search OR last_name LIKE :search OR medical_record_number LIKE :search
                ORDER BY first_name, last_name ASC
                LIMIT 50
            ");
            
            $exec_result = $stmt->execute([':search' => $search]);
            error_log("Execute result: " . ($exec_result ? 'true' : 'false'));
            
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Results count: " . count($results));
            
            if (!empty($results)) {
                foreach ($results as $r) {
                    error_log("  - Found: " . $r['first_name'] . " " . $r['last_name']);
                }
            }
            
            error_log("=== END SEARCH DEBUG ===");
            return $results;
        } catch (PDOException $e) {
            error_log("Patient Search Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate patient age from date of birth
     */
    public static function calculateAge($date_of_birth) {
        $birthdate = new DateTime($date_of_birth);
        $today = new DateTime();
        $interval = $today->diff($birthdate);
        return $interval->y;
    }
    
    /**
     * Update patient information
     */
    public function updatePatient($patient_id, $first_name, $last_name, $contact_phone = null, $contact_email = null) {
        try {
            $stmt = $this->db->prepare("
                UPDATE patients 
                SET first_name = :first_name, last_name = :last_name, 
                    contact_phone = :contact_phone, contact_email = :contact_email
                WHERE id = :id
            ");
            
            return $stmt->execute([
                ':id' => $patient_id,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':contact_phone' => $contact_phone,
                ':contact_email' => $contact_email
            ]);
        } catch (PDOException $e) {
            error_log("Patient Update Error: " . $e->getMessage());
            return false;
        }
    }
}
?>
