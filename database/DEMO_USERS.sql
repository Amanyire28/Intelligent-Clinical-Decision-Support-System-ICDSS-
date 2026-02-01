<?php
/**
 * ============================================================================
 * ICDSS DEMO USER SETUP
 * ============================================================================
 * 
 * This file contains SQL commands to create demo user accounts for testing.
 * Copy these commands into your MySQL console or phpMyAdmin.
 * 
 * Password Hashing Guide:
 * Use PHP to generate bcrypt hashes for passwords:
 * 
 *   <?php
 *   echo password_hash('your_password', PASSWORD_DEFAULT);
 *   ?>
 * 
 * Pre-generated hashes (for quick setup):
 * - password123: $2y$10$VEV4d02M3...
 * - admin123: $2y$10$KJHgfd87A...
 * 
 * For production, generate your own hashes!
 * ============================================================================
 */

/*
 * DOCTOR ACCOUNT
 * Username: doctor1
 * Email: doctor1@icdss.local
 * Password: password123
 * Role: doctor
 * Specialization: Oncology
 */

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
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', /* password123 */
    'Dr. Sarah Johnson',
    'doctor',
    'Oncology',
    'LIC2024001',
    TRUE
);

/*
 * ADMIN ACCOUNT
 * Username: admin1
 * Email: admin1@icdss.local
 * Password: admin123
 * Role: admin
 */

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
    '$2y$10$sUhXr7XB0lZQDwXjT8TlI.8sMvlB.Fms9Ag8pQlmYGlLVZHT/1Zqq', /* admin123 */
    'System Administrator',
    'admin',
    TRUE
);

/*
 * OPTIONAL: Additional Doctor Accounts
 */

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
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', /* password123 */
    'Dr. Michael Chen',
    'doctor',
    'ENT',
    'LIC2024002',
    TRUE
);

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
    '$2y$10$N9qo8uLOickgx2ZMRZoMyeIjZAgcg7b3XeKeUxWdeS86E36P4/KFm', /* password123 */
    'Dr. Lisa Rodriguez',
    'doctor',
    'Radiation Oncology',
    'LIC2024003',
    TRUE
);

/*
 * LOGIN CREDENTIALS FOR TESTING
 * ============================================================================
 * 
 * DOCTOR ACCESS:
 * Username: doctor1
 * Password: password123
 * 
 * Features Available:
 * - Create new patient assessments
 * - View assessment results
 * - Record clinical actions
 * - View patient history
 * 
 * ADMIN ACCESS:
 * Username: admin1
 * Password: admin123
 * 
 * Features Available:
 * - View all assessments
 * - Monitor system statistics
 * - Manage doctor accounts
 * - View risk distribution
 * - Monitor high-risk patients
 * - View system activity logs
 * 
 * ============================================================================
 * 
 * TESTING WORKFLOW:
 * 
 * 1. Login as doctor1
 * 2. Create a new assessment
 * 3. Fill in patient information
 * 4. Enter symptoms and medical history
 * 5. Submit the assessment
 * 6. View the risk results
 * 7. Record a clinical action
 * 8. Logout and login as admin1
 * 9. View the assessment in admin dashboard
 * 10. Monitor statistics
 * 
 * ============================================================================
 */

/*
 * GENERATING BCRYPT HASHES
 * 
 * If you need to create different passwords, use this PHP code:
 * 
 * <?php
 * // Generate hash for 'your_password'
 * $hash = password_hash('your_password', PASSWORD_DEFAULT);
 * echo $hash;
 * 
 * // This will output something like:
 * // $2y$10$KJHgfd87A...
 * ?>
 * 
 * Then use the generated hash in your INSERT statement above.
 */

/*
 * SAMPLE DATA INSERTION
 * 
 * After setting up users, you can insert sample patients and assessments:
 */

-- Sample Patient
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

/*
 * ============================================================================
 * IMPORTANT SECURITY NOTES
 * ============================================================================
 * 
 * 1. CHANGE DEFAULT PASSWORDS
 *    Never use demo passwords in production!
 *    Generate secure passwords using:
 *    - A password manager
 *    - Secure random generation
 * 
 * 2. UNIQUE HASHES
 *    The hashes provided above are EXAMPLES ONLY.
 *    Generate your own using the PHP password_hash() function.
 * 
 * 3. SECURE STORAGE
 *    Never store plain-text passwords.
 *    Always use bcrypt, Argon2, or similar.
 * 
 * 4. ACCESS CONTROL
 *    Ensure only authorized personnel have access.
 *    Use strong admin passwords.
 * 
 * 5. AUDIT LOGGING
 *    All user actions are logged to system_logs table.
 *    Review logs regularly for security.
 * 
 * 6. DATABASE SECURITY
 *    - Use database user with limited permissions
 *    - Enable SSL/TLS for database connections
 *    - Regular backups
 *    - Restrict database access to trusted IPs
 * 
 * ============================================================================
 */

?>
