-- ============================================================================
-- PATIENT OUTCOMES TABLE MIGRATION
-- For tracking patient diagnosis, treatment, and follow-up outcomes
-- This enables population-level learning and cohort analysis
-- ============================================================================

-- Create patient_outcomes table
CREATE TABLE patient_outcomes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    assessment_id INT NOT NULL UNIQUE,  -- One outcome per assessment
    
    -- Diagnosis information
    final_diagnosis ENUM('Malignant', 'Benign', 'Pending', 'Unknown') DEFAULT 'Unknown',
    cancer_stage VARCHAR(20) DEFAULT NULL,  -- e.g., "Stage 1", "Stage 2A", "Stage 3B", "Stage 4"
    cancer_type VARCHAR(100) DEFAULT NULL,   -- e.g., "Squamous cell carcinoma", "Adenocarcinoma"
    
    -- Treatment information
    treatment_type VARCHAR(255) DEFAULT NULL, -- e.g., "Surgery, Chemotherapy", "Radiation"
    
    -- Timeline and outcome tracking
    outcome_date DATE NOT NULL,               -- When diagnosis was confirmed
    follow_up_status ENUM('NED', 'Recurrence', 'Progressive', 'Unknown') DEFAULT 'Unknown',
    -- NED = No Evidence of Disease (cancer-free)
    
    -- Survival tracking
    survival_status ENUM('Alive', 'Deceased') DEFAULT 'Alive',
    years_survived DECIMAL(5,2) DEFAULT NULL, -- For deceased: actual years survived
    
    -- Clinical notes
    notes TEXT DEFAULT NULL,
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    INDEX idx_patient_id (patient_id),
    INDEX idx_assessment_id (assessment_id),
    INDEX idx_diagnosis (final_diagnosis),
    INDEX idx_outcome_date (outcome_date)
);

-- Add comparison columns to risk_results table (optional, for tracking)
ALTER TABLE risk_results ADD COLUMN (
    comparison_data JSON DEFAULT NULL COMMENT 'Stores comparison metadata from RiskComparisonEngine',
    trend_direction ENUM('improving', 'stable', 'worsening') DEFAULT NULL,
    adjustment_factor DECIMAL(5,2) DEFAULT 0
);

-- Create view for cohort analysis
CREATE VIEW cohort_analysis AS
SELECT 
    COUNT(DISTINCT po.patient_id) as total_cohort_size,
    COUNT(DISTINCT CASE WHEN po.final_diagnosis = 'Malignant' THEN po.patient_id END) as malignancy_count,
    ROUND(COUNT(DISTINCT CASE WHEN po.final_diagnosis = 'Malignant' THEN po.patient_id END) * 100 
          / COUNT(DISTINCT po.patient_id), 1) as malignancy_percentage,
    COUNT(DISTINCT CASE WHEN po.survival_status = 'Alive' THEN po.patient_id END) as alive_count,
    ROUND(COUNT(DISTINCT CASE WHEN po.survival_status = 'Alive' THEN po.patient_id END) * 100 
          / COUNT(DISTINCT po.patient_id), 1) as survival_percentage,
    AVG(rr.risk_score) as average_risk_score,
    COUNT(DISTINCT po.outcome_date) as outcomes_recorded
FROM patient_outcomes po
LEFT JOIN assessments a ON po.assessment_id = a.id
LEFT JOIN risk_results rr ON a.id = rr.assessment_id
WHERE po.final_diagnosis IS NOT NULL;
