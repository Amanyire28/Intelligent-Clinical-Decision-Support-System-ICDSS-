-- ============================================================================
-- INTELLIGENT CLINICAL DECISION SUPPORT SYSTEM - DATABASE SCHEMA
-- Throat Cancer Risk Assessment
-- ============================================================================

-- Create users table for doctors and admins
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(150) NOT NULL,
    role ENUM('doctor', 'admin') NOT NULL DEFAULT 'doctor',
    specialization VARCHAR(100) DEFAULT NULL,
    license_number VARCHAR(50) UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create patients table to store patient demographics
CREATE TABLE patients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    contact_phone VARCHAR(20),
    contact_email VARCHAR(100),
    medical_record_number VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create assessments table for risk evaluations
CREATE TABLE assessments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,
    assessment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    -- Lifestyle factors
    smoking_status ENUM('never', 'former', 'current') NOT NULL,
    smoking_years INT DEFAULT 0,
    alcohol_consumption ENUM('none', 'mild', 'moderate', 'heavy') DEFAULT 'none',
    -- Symptoms
    sore_throat_duration INT DEFAULT 0, -- Duration in weeks
    voice_changes BOOLEAN DEFAULT FALSE,
    difficulty_swallowing BOOLEAN DEFAULT FALSE,
    neck_lump BOOLEAN DEFAULT FALSE,
    weight_loss BOOLEAN DEFAULT FALSE,
    weight_loss_percentage DECIMAL(5,2) DEFAULT 0, -- Percentage loss
    unexplained_weight_loss_weeks INT DEFAULT 0, -- Duration in weeks
    -- Medical history
    hpv_status ENUM('positive', 'negative', 'unknown') DEFAULT 'unknown',
    hpv_test_date DATE DEFAULT NULL,
    family_cancer_history BOOLEAN DEFAULT FALSE,
    family_cancer_type VARCHAR(100) DEFAULT NULL,
    -- Lab indicators
    hemoglobin_level DECIMAL(4,2) DEFAULT NULL, -- g/dL
    lymphocyte_count DECIMAL(6,2) DEFAULT NULL,
    -- Clinical notes
    clinical_notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Create risk_results table to store risk assessment outputs
CREATE TABLE risk_results (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assessment_id INT NOT NULL,
    risk_score DECIMAL(5,2) DEFAULT 0, -- 0-100 scale
    risk_level ENUM('Low', 'Moderate', 'High') DEFAULT 'Low',
    confidence_percentage DECIMAL(5,2) DEFAULT 0, -- 0-100
    -- Core risk factors contributing to the score (for explainability)
    primary_factors VARCHAR(500) DEFAULT NULL, -- Comma-separated factors
    secondary_factors VARCHAR(500) DEFAULT NULL,
    clinical_recommendation TEXT DEFAULT NULL,
    -- TODO: Store intermediate scoring variables for auditability
    scoring_details JSON DEFAULT NULL, -- For Phase 4 implementation
    calculated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
);

-- Create action_history table to track clinical decisions
CREATE TABLE action_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    assessment_id INT NOT NULL,
    doctor_id INT NOT NULL,
    action_type ENUM('referred', 'monitoring', 'cleared', 'additional_tests') NOT NULL,
    action_notes TEXT,
    follow_up_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id) ON DELETE RESTRICT
);

-- Create system_logs table for audit trail
CREATE TABLE system_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    resource_type VARCHAR(100),
    resource_id INT,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Create indexes for performance optimization
CREATE INDEX idx_assessment_patient ON assessments(patient_id);
CREATE INDEX idx_assessment_doctor ON assessments(doctor_id);
CREATE INDEX idx_assessment_date ON assessments(assessment_date);
CREATE INDEX idx_risk_result_assessment ON risk_results(assessment_id);
CREATE INDEX idx_action_history_assessment ON action_history(assessment_id);
CREATE INDEX idx_system_logs_user ON system_logs(user_id);
CREATE INDEX idx_system_logs_timestamp ON system_logs(created_at);
