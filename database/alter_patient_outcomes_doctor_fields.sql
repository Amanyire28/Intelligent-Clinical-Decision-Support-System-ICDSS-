-- ============================================================================
-- ALTER patient_outcomes TABLE - Add Doctor's Clinical Form Fields
-- ============================================================================

-- Add columns for comprehensive outcome tracking
ALTER TABLE patient_outcomes ADD COLUMN (
    treatment_plan VARCHAR(500) DEFAULT NULL COMMENT 'Comma-separated treatment methods (Surgery, Radiation, etc.)',
    treatment_urgency VARCHAR(50) DEFAULT NULL COMMENT 'Immediate, Urgent, Standard, Non-urgent',
    clinical_findings TEXT DEFAULT NULL COMMENT 'Doctor clinical findings and observations',
    recommendations TEXT DEFAULT NULL COMMENT 'Doctor recommendations for patient management',
    follow_up_date DATE DEFAULT NULL COMMENT 'Recommended follow-up appointment date',
    tumor_location VARCHAR(100) DEFAULT NULL COMMENT 'Location of tumor if applicable'
);

-- Update the outcome_date to allow NULL since we\'ll use recorded_at timestamp
ALTER TABLE patient_outcomes MODIFY outcome_date DATE DEFAULT NULL;

-- Create a recorded_at timestamp if it doesn\'t exist
ALTER TABLE patient_outcomes ADD COLUMN recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When the outcome was recorded by doctor';

-- Create index for faster queries
CREATE INDEX idx_recorded_at ON patient_outcomes(recorded_at);
