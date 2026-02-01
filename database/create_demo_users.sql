-- ============================================================================
-- ICDSS DEMO USERS
-- ============================================================================

-- Doctor Account
INSERT INTO users (
    username,
    email,
    password_hash,
    full_name,
    role,
    specialization,
    license_number,
    is_active
) VALUES (
    'doctor1',
    'doctor1@icdss.local',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm',
    'Dr. Sarah Johnson',
    'doctor',
    'Oncology',
    'LIC2024001',
    TRUE
);

-- Admin Account
INSERT INTO users (
    username,
    email,
    password_hash,
    full_name,
    role,
    is_active
) VALUES (
    'admin1',
    'admin1@icdss.local',
    '$2y$10$sUhXr7XB0lZQDwXjT8TlI.8sMvlB.Fms9Ag8pQlmYGlLVZHT/1Zqq',
    'System Administrator',
    'admin',
    TRUE
);

-- Additional Doctor 2
INSERT INTO users (
    username,
    email,
    password_hash,
    full_name,
    role,
    specialization,
    license_number,
    is_active
) VALUES (
    'doctor2',
    'doctor2@icdss.local',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm',
    'Dr. Michael Chen',
    'doctor',
    'ENT',
    'LIC2024002',
    TRUE
);

-- Additional Doctor 3
INSERT INTO users (
    username,
    email,
    password_hash,
    full_name,
    role,
    specialization,
    license_number,
    is_active
) VALUES (
    'doctor3',
    'doctor3@icdss.local',
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm',
    'Dr. Lisa Rodriguez',
    'doctor',
    'Radiation Oncology',
    'LIC2024003',
    TRUE
);

-- Sample Patient 1
INSERT INTO patients (
    first_name,
    last_name,
    date_of_birth,
    gender,
    contact_phone,
    contact_email,
    medical_record_number
) VALUES (
    'John',
    'Smith',
    '1965-03-15',
    'Male',
    '555-1234',
    'john.smith@email.com',
    'MRN000001'
);

-- Sample Patient 2
INSERT INTO patients (
    first_name,
    last_name,
    date_of_birth,
    gender,
    contact_phone,
    contact_email,
    medical_record_number
) VALUES (
    'Jane',
    'Williams',
    '1972-07-22',
    'Female',
    '555-5678',
    'jane.williams@email.com',
    'MRN000002'
);

-- Password Information (stored as comment for reference):
-- doctor1 / password123
-- doctor2 / password123
-- doctor3 / password123
-- admin1 / admin123
