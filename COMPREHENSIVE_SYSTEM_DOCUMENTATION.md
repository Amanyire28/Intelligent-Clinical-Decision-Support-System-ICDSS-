# 🏥 CANCER ICDSS - Comprehensive System Documentation

**Project:** Intelligent Clinical Decision Support System for Throat Cancer Risk Assessment  
**Version:** 1.0  
**Date:** February 4, 2026  
**Status:** Phase 1-2 Complete, Phase 3-4 In Development

---

## 📑 Table of Contents

1. [System Overview](#system-overview)
2. [Architecture & Technology Stack](#architecture--technology-stack)
3. [Core Functionalities](#core-functionalities)
   - [1. User Authentication & Session Management](#1-user-authentication--session-management)
   - [2. Patient Management](#2-patient-management)
   - [3. Risk Assessment System](#3-risk-assessment-system)
   - [4. Risk Scoring & Results](#4-risk-scoring--results)
   - [5. Assessment Results Display](#5-assessment-results-display)
   - [6. Doctor Dashboard](#6-doctor-dashboard)
   - [7. Historical Data & Risk Comparison](#7-historical-data--risk-comparison)
   - [8. Population Learning](#8-population-learning)
4. [Admin Features](#admin-features)
5. [Database Schema](#database-schema)
6. [Data Flow](#data-flow)
7. [Security & Compliance](#security--compliance)
8. [User Workflows](#user-workflows)
9. [Setup & Deployment](#setup--deployment)
10. [API Endpoints](#api-endpoints)

---

## System Overview

The CANCER ICDSS is a hospital-grade clinical decision support system designed to assist doctors in early throat cancer risk assessment through:

- **Structured clinical evaluation** - Comprehensive symptom, lifestyle, and lab data collection
- **Intelligent risk scoring** - Evidence-based calculation of malignancy risk (0-100 scale)
- **Historical comparison** - Trend detection using patient's own assessment history
- **Population-level learning** - Context from similar historical cases
- **Doctor-centric interface** - Hospital-grade UI optimized for clinical workflow

### Key Features

✅ **Complete** - Phase 1-2 (Frontend, Database, Models)  
⏳ **In Development** - Phase 3-4 (Full integration, ML risk engine)

```
Features Completed:
├─ User authentication (Doctor/Admin roles)
├─ Patient management (registration, search, follow-up)
├─ Assessment form (structured data collection)
├─ Risk result display (score, level, confidence)
├─ Doctor dashboard (quick stats, recent assessments)
├─ Admin dashboard (system analytics)
├─ Historical comparison engine (trend detection)
├─ Population analytics (cohort insights)
└─ Audit logging (HIPAA compliance)
```

---

## Architecture & Technology Stack

### Technology Stack

```
Frontend:
├─ HTML5 (semantic markup)
├─ CSS3 (hospital-grade styling, responsive design)
└─ JavaScript (client-side validation, AJAX)

Backend:
├─ PHP 7+ (MVC architecture)
├─ PDO (database abstraction)
└─ bcrypt (password hashing)

Database:
├─ MySQL 5.7+
├─ Normalized schema (6 main tables)
├─ Foreign key constraints (data integrity)
└─ Indexes (query optimization)

Security:
├─ Session-based authentication
├─ Role-based access control (RBAC)
├─ Input sanitization
├─ Prepared statements (SQL injection prevention)
└─ Audit logging (compliance tracking)
```

### Directory Structure

```
/CANCER/
├── /config/                    # Configuration
│   └── db_config.php           # Database connection, utilities
├── /controllers/               # Business logic
│   ├── AuthController.php      # Authentication
│   ├── DoctorController.php    # Doctor dashboard
│   ├── AdminController.php     # Admin dashboard
│   ├── AssessmentController.php# Assessment operations
│   ├── PatientController.php   # Patient management
│   └── ActionController.php    # Clinical actions
├── /models/                    # Data models
│   ├── User.php                # User CRUD
│   ├── Patient.php             # Patient CRUD
│   ├── Assessment.php          # Assessment CRUD
│   ├── PatientOutcome.php      # Outcome tracking
│   ├── RiskResult.php          # Risk result storage
│   ├── RiskScoringEngine.php   # Risk calculation
│   ├── RiskComparisonEngine.php# Historical comparison
│   └── HistoricalAnalytics.php # Population analysis
├── /views/                     # HTML templates
│   ├── login.php               # Login page
│   ├── doctor_dashboard.php    # Doctor main view
│   ├── patient_assessment.php  # Assessment form
│   ├── assessment_results.php  # Results display
│   └── admin_dashboard.php     # Admin main view
├── /assets/                    # Static resources
│   ├── /css/
│   │   └── style.css           # Styling (1050+ lines)
│   └── /js/
│       ├── form_validation.js  # Client-side validation
│       ├── dashboard.js        # Dashboard utilities
│       └── admin.js            # Admin utilities
├── /database/                  # Database files
│   ├── schema.sql              # Complete schema
│   ├── migration_patient_outcomes.sql
│   └── DEMO_USERS.sql          # Demo data
└── index.php                   # Main router
```

---

## Core Functionalities

### 1. User Authentication & Session Management

**Files:**
- `controllers/AuthController.php`
- `models/User.php`
- `views/login.php`
- `config/db_config.php`

#### Overview

Two-role authentication system with secure password hashing and session management:
- **Doctor**: Can create assessments, view results, record outcomes
- **Admin**: Can view all assessments, manage system, view analytics

#### How It Works

```
LOGIN FLOW:
├─ User enters username/password on login.php
├─ Form submitted to AuthController::login()
├─ User::authenticate() verifies against database
│  ├─ SELECT from users table by username
│  ├─ password_verify() compares with bcrypt hash
│  └─ Returns user data or FALSE
├─ If successful:
│  ├─ Create session ($_SESSION['user_id', 'user_role', etc])
│  ├─ Regenerate session ID (security)
│  ├─ Log to system_logs (audit trail)
│  └─ Redirect to dashboard
└─ If failed:
   └─ Redirect to login with error message

SESSION TIMEOUT:
├─ Default: 30 minutes of inactivity
├─ Check on each page load: isset($_SESSION['user_id'])
├─ If expired: Redirect to login
└─ Automatic cleanup via PHP session handler

LOGOUT:
├─ User clicks logout
├─ AuthController::logout() called
├─ session_destroy() erases all session data
├─ Log to system_logs (audit trail)
└─ Redirect to login
```

#### Case Study: Dr. Sarah's Login

**Scenario:**
- Dr. Sarah Ahmed, ENT specialist
- Username: `sarah.ahmed`, Password: `SecurePass123!`
- Comes to work Tuesday morning, February 4, 2026

**Process:**

```
08:15 - Dr. Sarah visits index.php
├─ Router checks: isset($_SESSION['user_id'])? → NO
├─ Redirect to login page (views/login.php)
└─ Page loads with username/password fields

08:16 - Dr. Sarah enters credentials
├─ Username: sarah.ahmed
├─ Password: SecurePass123!
└─ Clicks "Login"

08:17 - Authentication process
├─ AuthController::login() receives form data
├─ User::authenticate('sarah.ahmed', 'SecurePass123!')
├─ Database query: SELECT * FROM users WHERE username = 'sarah.ahmed'
├─ Found: User ID 42, role 'doctor'
├─ Compare password:
│  ├─ Stored hash: $2y$10$ZjF3kL9nQxRmL2pW9vH8hOzK7dF4bN3mQ5sT6uV7wX8yZ9aB1cD2e
│  ├─ password_verify('SecurePass123!', stored_hash) → TRUE
│  └─ Authentication successful!
├─ Create session:
│  ├─ $_SESSION['user_id'] = 42
│  ├─ $_SESSION['user_role'] = 'doctor'
│  ├─ $_SESSION['user_name'] = 'Sarah Ahmed'
│  ├─ $_SESSION['specialization'] = 'ENT'
│  └─ session_regenerate_id(true)
├─ Log to system_logs:
│  ├─ user_id: 42
│  ├─ action: 'login'
│  ├─ description: 'Sarah Ahmed logged in'
│  └─ timestamp: 2026-02-04 08:15:32
└─ Redirect to: index.php?page=doctor-dashboard

08:18 - Dr. Sarah sees dashboard
├─ Router checks: $_SESSION['user_role'] === 'doctor'? → YES
├─ Check access: isset($_SESSION['user_id'])? → YES
└─ Load views/doctor_dashboard.php

14:00 - Session timeout
├─ Dr. Sarah returns after lunch
├─ Makes request to system
├─ Router checks: isset($_SESSION['user_id'])? → NO (expired)
├─ Redirect to login
└─ Message: "Session expired. Please log in again"

14:05 - Dr. Sarah logs in again
└─ Same process as first login

17:00 - Dr. Sarah clicks Logout
├─ AuthController::logout() called
├─ session_destroy() executed
├─ Log to system_logs: 'logout' action
└─ Redirect to login with success message
```

#### Security Features

```
Password Security:
├─ Stored as bcrypt hash: $2y$10$...
├─ Never stored as plaintext
├─ Each hash has unique salt
├─ Even with database leak, passwords safe
└─ password_verify() for comparison

Session Security:
├─ Session ID regenerated after login
├─ Session data stored server-side (not in cookie)
├─ Only PHPSESSID cookie sent to browser
├─ Cookie httponly flag (JavaScript cannot access)
└─ Secure flag (HTTPS only in production)

Access Control:
├─ Role-based (doctor vs admin)
├─ Checked on every protected page
├─ Doctor cannot access admin pages
├─ Admin can view all data
└─ Unauthorized access denied silently

Audit Trail:
├─ Every login logged to system_logs
├─ Every logout logged
├─ Failed attempts can be tracked (future feature)
└─ WHO, WHEN, WHERE recorded for compliance
```

#### Database Tables Involved

```sql
users table:
├─ id (Primary key)
├─ username (Unique)
├─ email
├─ password_hash (bcrypt)
├─ full_name
├─ specialization
├─ license_number
├─ role (ENUM: doctor, admin)
├─ is_active (Boolean)
└─ timestamps (created_at, updated_at)

system_logs table (audit trail):
├─ id (Primary key)
├─ user_id (FK to users)
├─ action (e.g., 'login', 'logout')
├─ resource_type
├─ resource_id
├─ description
└─ created_at
```

---

### 2. Patient Management

**Files:**
- `models/Patient.php`
- `controllers/PatientController.php`
- `views/patient_assessment.php`

#### Overview

Prevents duplicate patient records and enables follow-up assessments through intelligent patient selection:

- **New Patients**: Create fresh patient record
- **Returning Patients**: Search existing patients, use same patient ID for follow-ups

#### How It Works

```
TWO-PATH SYSTEM:

Path 1: New Patient
├─ Doctor clicks "New Patient"
├─ Form displays empty fields
├─ Doctor fills demographics
├─ Form submitted with patient_id = NULL
├─ Patient::createPatient() inserts new record
└─ Patient_id generated and linked to assessment

Path 2: Returning Patient
├─ Doctor clicks "Returning Patient"
├─ Search box appears (min 2 characters)
├─ AJAX search: PatientController::searchPatientAPI()
├─ Query: LIKE search on first_name, last_name, MRN
├─ Results show: Name, MRN, Age, Last assessment date
├─ Doctor selects patient from results
├─ Demographics pre-fill (read-only)
├─ Previous assessments shown in history panel
├─ New clinical data entered
├─ Form submitted with patient_id = EXISTING_ID
└─ Assessment linked to same patient (no duplicate!)
```

#### Case Study 1: New Patient - James Wilson

**Scenario:**
- James Wilson, 54-year-old with sore throat
- New to clinic, first time

**Process:**

```
STEP 1: New Patient Selected
├─ Form displays empty all fields
├─ Form title: "New Patient Assessment"
└─ No history panel visible

STEP 2: Demographics Entered
├─ First Name: James
├─ Last Name: Wilson
├─ Date of Birth: 1978-03-15
├─ Gender: Male
├─ Contact Phone: 555-0142
├─ Contact Email: james.wilson@email.com
├─ Medical Record Number: MRN-2026-00847
└─ hidden field: patient_id = NULL

STEP 3: Clinical Data Entered
├─ Sore throat duration: 6 weeks
├─ Voice changes: Yes
├─ Difficulty swallowing: No
├─ Neck lump: Yes (2cm, left side)
├─ Throat pain: 6/10
├─ Weight loss: 8 pounds
├─ Smoking: Current (28 years)
├─ Alcohol: Moderate
├─ HPV status: Unknown
├─ Family history: Yes (father, lung cancer)
├─ Labs: Hemoglobin 13.2, Lymphocytes 22%, WBC 8.5
└─ Clinical notes: [full assessment text]

STEP 4: Form Submitted
├─ POST to submit_assessment.php
├─ patient_id = NULL (not provided)
├─ Server detects: New patient needed

STEP 5: Database Operations
├─ Patient::createPatient() called
│  ├─ INSERT into patients:
│  │  ├─ first_name: 'James'
│  │  ├─ last_name: 'Wilson'
│  │  ├─ date_of_birth: '1978-03-15'
│  │  ├─ gender: 'Male'
│  │  └─ (contact info, MRN)
│  └─ Returns: patient_id = 156 (auto-increment)
│
├─ Assessment::createAssessment() called
│  ├─ INSERT into assessments:
│  │  ├─ patient_id: 156 (new)
│  │  ├─ doctor_id: 42
│  │  ├─ (all clinical data)
│  │  └─ assessment_date: NOW()
│  └─ Returns: assessment_id = 847
│
├─ RiskScoringEngine calculates: 61 (MODERATE)
│
└─ RiskResult stored

Database Result:
├─ patients table: 1 new row (id=156, James Wilson)
├─ assessments table: 1 new row (id=847, patient_id=156)
└─ risk_results table: 1 new row (assessment_id=847, score=61)
```

#### Case Study 2: Returning Patient - Michael Johnson

**Scenario:**
- Michael Johnson visited 6 months ago (assessment #1)
- Now returning for follow-up

**Process:**

```
STEP 1: Returning Patient Selected
├─ Search box appears
├─ Doctor types: "Michael"
└─ AJAX triggers (min 2 characters)

STEP 2: Search Results
├─ PatientController::searchPatientAPI() called
├─ Query: 
│  └─ SELECT * FROM patients 
│     WHERE first_name LIKE '%Michael%'
│     OR last_name LIKE '%Michael%'
├─ Also gets: MAX(assessment_date) for each patient
├─ Returns JSON:
│  ├─ id: 155
│  ├─ name: 'Michael Johnson'
│  ├─ mrn: 'MRN-2025-00521'
│  ├─ age: 54
│  └─ last_assessment: '2025-08-22 (6 months ago)'
└─ Displayed in search results table

STEP 3: Patient Selected
├─ Doctor clicks "Select Patient" button
├─ JavaScript calls selectPatient(155, 'Michael', 'Johnson', DOB, 'Male')
├─ patient_id = 155 stored in hidden field
├─ Demographics auto-populate:
│  ├─ First Name: Michael (DISABLED/READ-ONLY)
│  ├─ Last Name: Johnson (DISABLED)
│  ├─ Date of Birth: 1972-01-10 (DISABLED)
│  └─ Gender: Male (DISABLED)
├─ Note: "These fields are locked to your previous registration"

STEP 4: History Panel Displayed
├─ Fetch: getPatientAssessments(155)
├─ Query: SELECT * FROM assessments WHERE patient_id = 155
├─ Results: Assessment #1 (August 22, 2025)
│  ├─ Date: 2025-08-22
│  ├─ Risk Level: 🟠 Moderate (52)
│  ├─ Outcome Status: ⏳ Pending
│  └─ [View Details] button
├─ Note: "Review above history before creating new assessment..."
└─ Form title: "Follow-up Assessment #2"

STEP 5: New Clinical Data Entered (Demographics locked)
├─ Sore throat duration: 8 weeks (was 4 weeks) ← CHANGED
├─ Voice changes: No (unchanged)
├─ Difficulty swallowing: YES (was NO) ← NEW!
├─ Smoking: Former (was Current) ← QUIT!
├─ Alcohol: Mild (was Moderate) ← REDUCED
├─ Labs: Hemoglobin 12.9 (was 13.8) ← DECLINED
└─ (Other new data)

STEP 6: Form Submitted
├─ POST to submit_assessment.php
├─ patient_id = 155 (provided from previous step)
├─ Server detects: patient_id provided
├─ Skip patient creation
├─ Use existing patient

STEP 7: Database Operations
├─ Patient table: NO CHANGE (still 155)
├─ Assessment::createAssessment() called
│  ├─ INSERT into assessments:
│  │  ├─ patient_id: 155 (SAME!)
│  │  ├─ doctor_id: 42
│  │  ├─ assessment_date: 2026-02-04
│  │  └─ (new clinical data)
│  └─ Returns: assessment_id = 848
│
├─ RiskScoringEngine calculates base: 56
├─ RiskComparisonEngine::analyzeWithHistory()
│  ├─ Fetches previous assessments: getPatientAssessments(155)
│  ├─ Found: [assessment #847 from 2025-08-22]
│  ├─ Compares against new data
│  ├─ Detects: Difficulty swallowing (NEW), Lab decline
│  ├─ Adjustment: +15 points
│  └─ Final score: 56 + 15 = 71 (HIGH)
│
└─ RiskResult stored with comparison data

Database Result:
├─ patients table: UNCHANGED (still id=155)
├─ assessments table: 2 rows for patient_id=155
│  ├─ Row 1: id=847, date=2025-08-22, score=52
│  └─ Row 2: id=848, date=2026-02-04, score=71
└─ risk_results table: 
   ├─ Row 1: assessment_id=847, score=52
   └─ Row 2: assessment_id=848, score=71, adjustment_factor=+15
```

#### Key Benefit: No Duplicates!

```
BEFORE (without patient selection):
├─ 1st visit: Create patient "Michael Johnson" → id=101
├─ 6 months later: "Michael Johnson" again
├─ Unknown it's same person
└─ Create duplicate → id=102
   └─ NOW: 2 separate patient records
      ├─ Can't compare assessments (different patient IDs)
      ├─ Historical data scattered across 2 records
      └─ Risk comparison IMPOSSIBLE

AFTER (with patient selection):
├─ 1st visit: Create patient "Michael Johnson" → id=155
├─ 6 months later: SEARCH for "Michael"
├─ SELECT existing patient → id=155
└─ NOW: 1 patient record with 2 assessments
   ├─ Both assessments linked to patient_id=155
   ├─ Can fetch all assessments for patient
   └─ Risk comparison WORKS! ✅
```

#### Patient Search API

```php
PatientController::searchPatientAPI()
├─ Triggered by: AJAX on minimum 2 characters
├─ Input: POST search_term
├─ Query:
│  └─ SELECT id, first_name, last_name, date_of_birth, gender, 
│     medical_record_number, 
│     MAX(a.assessment_date) as last_assessment
│     FROM patients p
│     LEFT JOIN assessments a ON p.id = a.patient_id
│     WHERE p.first_name LIKE ? OR p.last_name LIKE ? 
│     OR p.medical_record_number LIKE ?
│     GROUP BY p.id
├─ Output: JSON array of matching patients
└─ Returns: id, name, age, MRN, last_assessment_date
```

---

### 3. Risk Assessment System

**Files:**
- `models/Assessment.php`
- `controllers/AssessmentController.php`
- `views/patient_assessment.php`

#### Overview

Comprehensive structured data collection across 6 categories:
1. **Symptoms** - Throat-specific manifestations
2. **Lifestyle** - Smoking, alcohol, drug use
3. **Medical History** - HPV, family history, previous conditions
4. **Lab Indicators** - Blood work results
5. **Clinical Notes** - Doctor observations
6. **Contact Information** - Patient demographics

#### Assessment Form Structure

```
SYMPTOMS CATEGORY:
├─ Sore throat duration (weeks): 0-52
├─ Voice changes/Hoarseness: Boolean
├─ Difficulty swallowing: Boolean
├─ Neck lump/Mass: Boolean
├─ Throat pain (0-10 scale): 0-10
├─ Weight loss (% in time period): 0-100%
└─ Ear pain: Boolean
└─ Red/white patches: Boolean

LIFESTYLE CATEGORY:
├─ Smoking status: ENUM (never, former, current)
├─ Years of smoking: 0-80
├─ Cigarettes per day: 0-100
├─ Pack-year calculation: (cigs/20) × years
├─ Alcohol consumption: ENUM (none, mild, moderate, heavy)
├─ Drug use: Boolean
└─ Drug type (if yes): Text

MEDICAL HISTORY CATEGORY:
├─ HPV status: ENUM (positive, negative, unknown)
├─ Family cancer history: Boolean
├─ Family cancer details: Text
├─ Previous throat conditions: Boolean
├─ Previous condition types: Text
└─ Immunocompromised status: Boolean

LAB INDICATORS CATEGORY:
├─ Hemoglobin level (g/dL): 0-20
├─ Lymphocyte count (%): 0-100
├─ White blood cell count (K/uL): 0-50
├─ Platelet count (K/uL): 0-1000
└─ Other tests: Text

DEMOGRAPHICS:
├─ First name
├─ Last name
├─ Date of birth
├─ Gender: ENUM (Male, Female, Other)
├─ Contact phone
├─ Contact email
└─ Medical record number

CLINICAL NOTES:
└─ Textarea for doctor's observations
```

#### Case Study: James Wilson's Complete Assessment

**Scenario:**
Dr. Sarah completes a detailed assessment for James Wilson, 54-year-old with concerning throat symptoms.

**SYMPTOM COLLECTION:**

```
Dr. Sarah: "How long has the sore throat been going on?"
James: "About 6 weeks. Started right after New Year."
├─ Data stored: sore_throat_duration = 6 weeks
├─ Clinical significance: >4 weeks = chronic (concerning)
└─ Risk contribution: 10 points

Dr. Sarah: "Any voice changes?"
James: "Yeah, my voice got real raspy about 3 weeks ago."
├─ Data stored: voice_changes = 1 (TRUE)
├─ Clinical significance: Suggests laryngeal involvement
└─ Risk contribution: 8 points

Dr. Sarah: "Can you swallow normally?"
James: "It hurts but I can swallow okay."
├─ Data stored: difficulty_swallowing = 0 (FALSE)
├─ Clinical significance: No dysphagia (somewhat reassuring)
└─ Risk contribution: 0 points

Dr. Sarah: "Do you feel any lumps in your neck?"
James: "Yes, there's definitely a lump on the left side."
├─ Dr. Sarah palpates: Approximately 2cm, firm, non-mobile
├─ Data stored: neck_lump = 1 (TRUE)
├─ Clinical significance: LYMPH NODE ENLARGEMENT - HIGH RISK
└─ Risk contribution: 20 points ⚠️⚠️⚠️

Dr. Sarah: "On a scale of 0-10, how bad is the throat pain?"
James: "About a 6. Worse when I swallow or eat hot food."
├─ Data stored: throat_pain = 6
├─ Clinical significance: Moderate pain suggesting tissue involvement
└─ Risk contribution: 5 points

Dr. Sarah: "Any weight loss recently?"
James: "Yeah, I've lost about 8 pounds in the last 3 weeks."
├─ Baseline weight: 185 lbs
├─ Weight loss: 8 lbs / 185 lbs = 4.3%
├─ Timeframe: 3 weeks
├─ Data stored: weight_loss_percentage = 4.3
├─ Clinical significance: Unintentional loss >5% = concerning
└─ Risk contribution: 8 points
```

**LIFESTYLE COLLECTION:**

```
Dr. Sarah: "Do you smoke?"
James: "Yeah, been smoking since I was about 20. Almost every day."
├─ Data stored: smoking_status = 'current'
├─ Duration: 28 years (currently 54, started at 26)
├─ Data stored: smoking_years = 28
├─ Cigarettes per day: 1.5 packs (30 cigarettes)
├─ Data stored: cigarettes_per_day = 30
├─ Pack-year calculation: (30/20) × 28 = 42 pack-years
├─ Clinical significance: HEAVY SMOKER (>40 pack-years = high risk)
└─ Risk contribution: 8 + 12 = 20 points ⚠️⚠️⚠️

Dr. Sarah: "What about alcohol use?"
James: "I drink beer most nights. Usually 3-4 beers after work."
├─ Frequency: 5-6 nights per week
├─ Amount: 3-4 beers × 5-6 nights = 15-24 drinks/week
├─ Safe limit: <7 drinks/week for men
├─ James: 15-24 drinks = MODERATE TO HEAVY
├─ Data stored: alcohol_consumption = 'moderate-heavy'
├─ Clinical significance: Synergistic with smoking (15x risk increase)
└─ Risk contribution: 7 points

Dr. Sarah: "Any drug use? Marijuana, cocaine, etc?"
James: "Nope, just cigarettes and beer."
├─ Data stored: drug_use = FALSE
└─ Risk contribution: 0 points
```

**MEDICAL HISTORY COLLECTION:**

```
Dr. Sarah: "Have you ever been tested for HPV?"
James: "HPV? I don't even know what that is."
├─ Data stored: hpv_status = 'unknown'
├─ Clinical significance: Need HPV testing if biopsy obtained
└─ Risk contribution: 2 points

Dr. Sarah: "Any cancer in your family?"
James: "Yeah, my dad had lung cancer. He was 68 when they found it."
├─ Data stored: family_cancer_history = TRUE
├─ Family details: Father, lung cancer, age 68 at diagnosis
├─ Clinical significance: Genetic predisposition + shared smoking environment
└─ Risk contribution: 8 points

Dr. Sarah: "Has your throat been like this before?"
James: "I've had a sore throat on and off for years. Chronic."
├─ Data stored: previous_throat_issues = TRUE
├─ Issue type: Chronic pharyngitis
├─ Duration: Several years, recurring
├─ Clinical significance: Pre-existing inflammation = risk factor
└─ Risk contribution: 6 points

Dr. Sarah: "Are you on any immunosuppressive medications?"
James: "No, I'm generally healthy except for all this."
├─ Data stored: immunocompromised = FALSE
└─ Risk contribution: 0 points
```

**LAB COLLECTION:**

```
Recent lab work drawn (within past week):

Hemoglobin test result: 13.2 g/dL
├─ Normal range: 13.5-17.5 for adult males
├─ James: SLIGHTLY LOW (mild anemia)
├─ Significance: Can indicate chronic disease
├─ Data stored: hemoglobin_level = 13.2
└─ Risk contribution: 3 points

Lymphocyte count: 22%
├─ Normal range: 20-40%
├─ James: High-normal (upper range)
├─ Significance: May indicate immune response
├─ Data stored: lymphocyte_count = 22
└─ Risk contribution: 3 points

WBC (White Blood Cell) count: 8.5 K/uL
├─ Normal range: 4.5-11.0 K/uL
├─ James: NORMAL
├─ Significance: Rules out acute infection
├─ Data stored: white_blood_cell = 8.5
└─ Risk contribution: 0 points
```

**CLINICAL NOTES:**

```
Dr. Sarah types:

"54-year-old male presenting with 6-week history of persistent 
sore throat with hoarseness × 3 weeks. Physical exam reveals 
palpable left-sided neck mass approximately 2cm, firm, 
non-tender, non-mobile.

Associated with unintentional weight loss of 8 lbs over 3 weeks.

SIGNIFICANT RISK FACTORS:
• Very heavy smoking history (42 pack-years)
• Moderate-heavy alcohol use (15-24 drinks/week)
• Family history of cancer (father with lung cancer at age 68)
• Chronic recurrent pharyngitis (pre-existing inflammation)
• Systemic signs: Weight loss, mild anemia, elevated lymphocytes

ASSESSMENT:
High concern for possible head & neck malignancy with regional 
lymph node involvement. Clinical presentation concerning for 
laryngeal primary with cervical lymphadenopathy.

RECOMMENDATIONS:
1. URGENT ENT specialist referral (same week if possible)
2. Imaging: CT head/neck with IV contrast ± MRI
3. Possible fine needle aspiration (FNA) of neck mass
4. HPV testing (if biopsy tissue obtained)
5. Smoking cessation counseling
6. Alcohol reduction counseling

Prognosis: Guarded pending workup. Need rapid diagnostic evaluation 
given clinical presentation."

Data stored: clinical_notes = [full text above]
```

**DATABASE ENTRY:**

```sql
INSERT INTO assessments VALUES (
    847,                    -- id (auto-increment)
    156,                    -- patient_id (James Wilson)
    42,                     -- doctor_id (Dr. Sarah)
    6,                      -- sore_throat_duration
    1,                      -- voice_changes
    0,                      -- difficulty_swallowing
    1,                      -- neck_lump
    6,                      -- throat_pain
    4.3,                    -- weight_loss_percentage
    'current',              -- smoking_status
    28,                     -- smoking_years
    30,                     -- cigarettes_per_day
    'moderate-heavy',       -- alcohol_consumption
    0,                      -- drug_use
    'unknown',              -- hpv_status
    1,                      -- family_cancer_history
    1,                      -- previous_throat_issues
    0,                      -- immunocompromised
    13.2,                   -- hemoglobin_level
    22,                     -- lymphocyte_count
    8.5,                    -- white_blood_cell
    '[clinical notes...]',  -- clinical_notes
    '2026-02-04',           -- assessment_date
    NOW(),                  -- created_at
    NOW()                   -- updated_at
);
```

---

### 4. Risk Scoring & Results

**Files:**
- `models/RiskScoringEngine.php`
- `models/RiskComparisonEngine.php`
- `models/RiskResult.php`

#### Overview

Two-stage risk calculation:
1. **Base Risk Score** - From current assessment data (0-100)
2. **Adjusted Risk Score** - Refined by historical trends (±30 points possible)

#### Stage 1: Base Risk Score Calculation

```
SCORING FORMULA:

Four weighted categories:
├─ Symptoms: 40% weight
├─ Lifestyle: 30% weight
├─ Medical History: 20% weight
└─ Lab Indicators: 10% weight

SYMPTOM SCORING (40% weight):
├─ Sore throat duration
│  ├─ 0-2 weeks: 0 points
│  ├─ 2-4 weeks: 5 points
│  ├─ 4-6 weeks: 10 points
│  └─ >6 weeks: 15 points
├─ Voice changes: 8 points if YES
├─ Difficulty swallowing: 12 points if YES
├─ Neck lump: 20 points if YES (CRITICAL)
├─ Throat pain: 5 points if 4-6 scale, 10 if 7+
├─ Weight loss: 8 points if 2-5%, 15 if >5%
└─ Maximum: ~100 points

LIFESTYLE SCORING (30% weight):
├─ Smoking status: 8 points if current
├─ Pack-years: 3-12 points based on duration
├─ Alcohol: 2-10 points based on consumption
└─ Drug use: 8 points if YES

MEDICAL HISTORY (20% weight):
├─ HPV positive: 15 points
├─ Family cancer history: 8 points
├─ Previous throat issues: 6 points
└─ Immunocompromised: 5 points

LAB INDICATORS (10% weight):
├─ Hemoglobin <13.5: 3-8 points
├─ Elevated lymphocytes: 3 points
└─ Elevated WBC: 5 points

CALCULATION:
├─ Raw score each category
├─ Normalize to 0-100 per category
├─ Apply weights (40%, 30%, 20%, 10%)
├─ Sum weighted scores
└─ Final: 0-100 scale
```

#### Case Study: James Wilson's Risk Score Calculation

**SYMPTOM SCORING:**
```
Sore throat (6 weeks):
├─ 4-6 weeks range → 10 points
└─ As % of symptom max: 10/100 = 10%

Voice changes (YES):
├─ 8 points
└─ As % of symptom max: 8/100 = 8%

Difficulty swallowing (NO):
├─ 0 points
└─ As % of symptom max: 0/100 = 0%

Neck lump (YES):
├─ 20 points (CRITICAL)
└─ As % of symptom max: 20/100 = 20%

Throat pain (6/10):
├─ 4-6 range → 5 points
└─ As % of symptom max: 5/100 = 5%

Weight loss (4.3%):
├─ 2-5% range → 8 points
└─ As % of symptom max: 8/100 = 8%

Total raw symptoms: 10+8+0+20+5+8 = 51 points
Maximum possible: ~100 points
Symptom percentage: 51/100 = 51%
Weighted to 40%: 0.51 × 40 = 20.4 points contribution
```

**LIFESTYLE SCORING:**
```
Smoking (CURRENT):
├─ 8 points
└─ As % of lifestyle max: 8/38 = 21%

Pack-years (42):
├─ >40 pack-years → 12 points
└─ As % of lifestyle max: 12/38 = 32%

Alcohol (Moderate-Heavy):
├─ 7 points
└─ As % of lifestyle max: 7/38 = 18%

Drug use (NO):
├─ 0 points
└─ As % of lifestyle max: 0/38 = 0%

Total raw lifestyle: 8+12+7+0 = 27 points
Maximum possible: 38 points
Lifestyle percentage: 27/38 = 71%
Weighted to 30%: 0.71 × 30 = 21.3 points contribution
```

**MEDICAL HISTORY SCORING:**
```
HPV (UNKNOWN):
├─ 2 points
└─ As % of history max: 2/21 = 10%

Family cancer (YES):
├─ 8 points
└─ As % of history max: 8/21 = 38%

Previous throat issues (YES):
├─ 6 points
└─ As % of history max: 6/21 = 29%

Immunocompromised (NO):
├─ 0 points
└─ As % of history max: 0/21 = 0%

Total raw history: 2+8+6+0 = 16 points
Maximum possible: 21 points
History percentage: 16/21 = 76%
Weighted to 20%: 0.76 × 20 = 15.2 points contribution
```

**LAB INDICATORS SCORING:**
```
Hemoglobin (13.2 g/dL):
├─ Low-normal (13.0-13.5) → 3 points
└─ As % of lab max: 3/16 = 19%

Lymphocytes (22%):
├─ Elevated → 3 points
└─ As % of lab max: 3/16 = 19%

WBC (8.5 K/uL):
├─ Normal → 0 points
└─ As % of lab max: 0/16 = 0%

Total raw labs: 3+3+0 = 6 points
Maximum possible: 16 points
Lab percentage: 6/16 = 38%
Weighted to 10%: 0.38 × 10 = 3.8 points contribution
```

**FINAL CALCULATION:**
```
Category contributions:
├─ Symptoms (40%): 20.4 points
├─ Lifestyle (30%): 21.3 points
├─ Medical History (20%): 15.2 points
└─ Lab Indicators (10%): 3.8 points
────────────────────────────
TOTAL BASE RISK SCORE: 60.7 ≈ 61/100
```

#### Stage 2: Risk Level Classification

```
Score Ranges:

🟢 LOW RISK: 0-39
├─ Malignancy probability: 5-15%
├─ Recommendation: Routine monitoring
└─ Action: Reassure, education

🟠 MODERATE RISK: 40-64
├─ James: 61 (high-end moderate)
├─ Malignancy probability: 30-60%
└─ Recommendation: Urgent evaluation, imaging, consider biopsy

🔴 HIGH RISK: 65-100
├─ Malignancy probability: 70-95%
└─ Recommendation: STAT workup, assume cancer until proven otherwise
```

**James's Classification:**
```
Score: 61/100
Range: 40-64 (MODERATE)
Position: High-end of moderate (near boundary to HIGH)
Clinical significance: Not quite HIGH but requires appropriate urgency
```

#### Stage 3: Confidence Calculation

```
Base confidence varies by risk range:

LOW RISK (0-39):
├─ Base: 65%
└─ Reasoning: More reassuring, less concerning data

MODERATE RISK (40-64):
├─ Base: 75% (low-end) to 82% (high-end)
├─ Formula: 75 + (score-40)/25 × 7
└─ James (61): 75 + (61-40)/25 × 7 = 80.8%

HIGH RISK (65-100):
├─ Base: 85% (low-end) to 95% (high-end)
├─ Formula: 85 + (score-65)/35 × 10
└─ More confidence due to concerning data

ADJUSTMENTS to confidence:
├─ Multiple consistent factors: +3%
├─ Lab support for findings: +2%
├─ Patient history reliability: +1-3%
└─ First-time patient: -3% (less context)

James's Final Confidence:
├─ Base: 80.8%
├─ Multiple concerning symptoms: +3%
├─ Symptom consistency: +2%
├─ First visit (reduced confidence): -3%
├─ Final: 80.8% - 3% = 77.8% ≈ 78%

Interpretation:
├─ Dr. Sarah is 78% confident in this assessment
├─ Comfortable enough to act on it
├─ But not 95% certain (which would require more data)
└─ Reasonable to get specialist second opinion
```

#### Stage 4: Risk Result Stored

```sql
INSERT INTO risk_results VALUES (
    523,                   -- id (auto-increment)
    847,                   -- assessment_id
    61.0,                  -- risk_score
    'Moderate',            -- risk_level
    78.0,                  -- confidence_percentage
    'Persistent sore throat + neck mass + smoking',  -- primary_factors
    'Slight anemia + family history',                -- secondary_factors
    'Urgent ENT workup recommended...',              -- clinical_recommendation
    'baseline',            -- trend_direction (first-time patient)
    0,                     -- adjustment_factor
    NULL,                  -- comparison_data (no history)
    NOW(),                 -- created_at
    NOW()                  -- updated_at
);
```

#### Stage 5: Historical Comparison (If Patient Has History)

**Case Study: Michael Johnson's Follow-up with Adjustment**

```
Previous Assessment (August 2025):
├─ Base risk score: 52 (MODERATE)
├─ Risk level: Moderate

Current Assessment (February 2026, 6 months later):
├─ Base risk score: 56 (MODERATE)
└─ Difference: +4 points (minimal)

RiskComparisonEngine analysis:
├─ TREND ANALYSIS: +2 points (minor worsening)
├─ SYMPTOM PROGRESSION: +12 points
│  ├─ Sore throat: 4w → 8w (doubled): +5
│  └─ Difficulty swallowing: NEW: +7
├─ BEHAVIORAL CHANGES: -4 points
│  ├─ Quit smoking: -3 (good!)
│  └─ Reduced alcohol: -1
├─ LAB DECLINE: +5 points
│  ├─ Hemoglobin: 13.8 → 12.9: +3
│  └─ Lymphocytes elevated: +2
└─ TEMPORAL: 0 points (routine interval)

TOTAL ADJUSTMENT: +2 + 12 - 4 + 5 = +15 points

Final Adjusted Score:
├─ Base: 56
├─ Adjustment: +15
├─ Final: 56 + 15 = 71 (HIGH RISK!)
└─ Confidence: Increased from 76% to 94% (very confident)

Why increased risk despite quitting smoking?
├─ New dysphagia is serious indicator
├─ If smoking caused it, quitting would help
├─ Worsening despite quitting suggests malignancy
└─ Multiple independent factors pointing same direction
```

---

### 5. Assessment Results Display

**File:** `views/assessment_results.php`

#### Overview

Doctor-friendly results page displaying:
- Risk score and confidence
- Primary/secondary risk factors
- Clinical recommendations
- Trend analysis (if returning patient)
- Action forms for clinical decisions

#### What Doctor Sees

```
┌─────────────────────────────────────────────────────┐
│           ASSESSMENT RESULTS DISPLAY                │
│        (What Dr. Sarah sees on screen)              │
└─────────────────────────────────────────────────────┘

PATIENT INFORMATION CARD
┌─────────────────────────────────────────────────────┐
│ Name: James Wilson                MRN: MRN-2026-00847
│ DOB: 03/15/1978 (Age: 47)         Gender: Male
│ Contact: 555-0142 | james.wilson@email.com
│ Assessment: February 4, 2026       Assessment #1
└─────────────────────────────────────────────────────┘

RISK SCORE VISUALIZATION
┌─────────────────────────────────────────────────────┐
│                  ┌─────────────┐                    │
│                 /               \                   │
│                |       61       |                   │
│                |      /100      |                   │
│                 \               /                   │
│                  └─────────────┘                    │
│                                                     │
│           🟠 MODERATE RISK                         │
│      (High-end: near HIGH boundary)                │
│                                                     │
│  Confidence: ████████░░ 78%                       │
│  (78% confidence in assessment accuracy)          │
└─────────────────────────────────────────────────────┘

PRIMARY RISK FACTORS (What raised the score?)
┌─────────────────────────────────────────────────────┐
│ 🔴 Neck Mass/Lump (20 points) ← MOST CONCERNING  │
│ 🟠 Persistent Sore Throat (10 points)            │
│ 🟠 Voice Changes (8 points)                      │
│ 🟠 Heavy Smoking (12 pack-years)                 │
│ 🟠 Alcohol Consumption (7 points)                │
│ 🟠 Weight Loss 4.3% (8 points)                   │
│ 🟡 Family Cancer History (8 points)              │
│ 🟡 Chronic Throat Issues (6 points)              │
│ 🟡 Slight Anemia (3 points)                      │
└─────────────────────────────────────────────────────┘

CLINICAL INTERPRETATION
┌─────────────────────────────────────────────────────┐
│ WHAT THIS MEANS:                                    │
│                                                     │
│ This patient shows moderate risk for laryngeal     │
│ malignancy. While not in the highest category,     │
│ the combination of persistent symptoms, neck mass, │
│ heavy smoking, and family history suggests urgent  │
│ evaluation is warranted.                           │
│                                                     │
│ The confidence level of 78% indicates the          │
│ assessment is reliable, though specialist input    │
│ is recommended.                                    │
└─────────────────────────────────────────────────────┘

CLINICAL RECOMMENDATION
┌─────────────────────────────────────────────────────┐
│ RECOMMENDED NEXT STEPS:                            │
│                                                     │
│ URGENT (Within 1-2 weeks):                        │
│ ✓ Refer to Head & Neck Surgery/Oncology          │
│ ✓ CT scan of head and neck                       │
│ ✓ Fine needle aspiration (FNA) biopsy of mass   │
│ ✓ Laryngoscopy examination                       │
│                                                     │
│ CONCURRENT:                                       │
│ ✓ Baseline labs: CBC, metabolic panel            │
│ ✓ HPV testing                                    │
│ ✓ Smoking cessation counseling                  │
│ ✓ Alcohol reduction counseling                  │
│                                                     │
│ FOLLOW-UP:                                        │
│ ✓ Call with imaging results when available       │
│ ✓ Schedule 1-week follow-up if diagnosis needed  │
│ ✓ Oncology will manage treatment planning        │
└─────────────────────────────────────────────────────┘

ASSESSMENT DETAILS (Data Used in Calculation)
┌─────────────────────────────────────────────────────┐
│ SYMPTOMS:                MEDICAL HISTORY:          │
│ • Sore throat: 6 weeks   • HPV: Unknown           │
│ • Voice changes: Yes     • Family history: Yes     │
│ • Swallowing: Normal     • Previous issues: Yes    │
│ • Neck lump: Yes (2cm)   • Immunocompromised: No   │
│ • Pain: 6/10                                       │
│ • Weight loss: 8 lbs     LAB VALUES:              │
│                          • Hemoglobin: 13.2 g/dL  │
│ LIFESTYLE:               • Lymphocytes: 22%       │
│ • Smoking: Current       • WBC: 8.5 K/uL         │
│ • Pack-years: 42                                  │
│ • Alcohol: Moderate-H    DATES:                   │
│ • Drugs: None            • Assessment: 2/4/2026   │
│                          • Last assessment: N/A   │
│                          • Days since last: —      │
└─────────────────────────────────────────────────────┘

CLINICAL ACTION FORM (Doctor's Next Decision)
┌─────────────────────────────────────────────────────┐
│ RECORD YOUR ACTION:                                │
│                                                     │
│ Action Type: [Refer to Specialist ▼]             │
│ Specialty: [Head & Neck Surgery ▼]               │
│ Urgency: [URGENT ▼]                              │
│ Follow-up Date: [Fri, Feb 10, 2026]              │
│                                                     │
│ Clinical Notes:                                    │
│ ┌─────────────────────────────────────────────┐  │
│ │ "Strong suspicion for head/neck malignancy  │  │
│ │ given neck mass, persistent laryngeal       │  │
│ │ symptoms, and smoking/alcohol history.      │  │
│ │ Requires urgent imaging and biopsy. Patient │  │
│ │ counseled on findings and referral."        │  │
│ └─────────────────────────────────────────────┘  │
│                                                     │
│ [Submit & Refer] [Print] [Save Draft]            │
└─────────────────────────────────────────────────────┘
```

#### Trend Analysis (For Returning Patients)

```
EXAMPLE: Michael Johnson's Follow-up Results

Previous Assessment: August 22, 2025 (52 - MODERATE)
Current Assessment: February 4, 2026 (71 - HIGH)

TREND VISUALIZATION
┌─────────────────────────────────────────────────────┐
│ Score Trend: 52 ─────→ 71 (↑ +19 points)          │
│ Level: 🟠 Moderate → 🔴 HIGH                      │
│ Trend: ⬆️ WORSENING                               │
│ Severity: RAPID (+19 > +15 threshold)             │
└─────────────────────────────────────────────────────┘

DETAILED COMPARISON
┌─────────────────────────────────────────────────────┐
│ SYMPTOM CHANGES:                                    │
│ • Sore throat: 4 weeks → 8 weeks (DOUBLED)        │
│ • Difficulty swallowing: NO → YES (NEW FINDING)   │
│ • Lab hemoglobin: 13.8 → 12.9 (DECLINING)        │
│                                                     │
│ POSITIVE CHANGES:                                  │
│ ✅ Smoking: Current → Former (QUIT!)              │
│ ✅ Alcohol: Moderate → Mild (REDUCED)            │
│                                                     │
│ INTERPRETATION:                                    │
│ Despite positive behavioral changes, symptoms     │
│ worsened significantly. New dysphagia is          │
│ particularly concerning. Suggests disease         │
│ process rather than smoking-related irritation.   │
│                                                     │
│ ADJUSTMENT BREAKDOWN:                             │
│ • Base score from current data: 56                │
│ • Symptom progression adjustment: +12            │
│ • Behavioral improvement: -4                      │
│ • Lab decline: +5                                │
│ • Total adjustment: +15                          │
│ • Final score: 56 + 15 = 71 (HIGH)             │
│ • Confidence: 94% (very high, multiple factors)  │
└─────────────────────────────────────────────────────┘

COMPREHENSIVE INSIGHTS
┌─────────────────────────────────────────────────────┐
│ ⬆️ WORSENING TREND: Patient condition shows         │
│    progressive deterioration. Multiple risk factors │
│    increasing.                                      │
│                                                     │
│ 🔴 NEW SYMPTOMS: Difficulty swallowing (NEW,      │
│    HIGH CONCERN)                                    │
│                                                     │
│ 📈 WORSENING: Sore throat increased 4w → 8w      │
│                                                     │
│ ✅ POSITIVE: Quit smoking and reduced alcohol.     │
│    However, symptom progression takes precedence.  │
│                                                     │
│ 🩸 LAB VALUES: Hemoglobin declining toward         │
│    anemia range (13.8 → 12.9 g/dL)               │
│                                                     │
│ ⏰ PATTERN: 6 months with persistent/worsening     │
│    symptoms is concerning for chronic disease.     │
│                                                     │
│ 📋 SUMMARY: Recommend urgent specialist workup     │
│    with imaging and biopsy consideration.         │
└─────────────────────────────────────────────────────┘
```

---

### 6. Doctor Dashboard

**File:** `views/doctor_dashboard.php`

#### Overview

Central hub for doctors to:
- View quick statistics
- Access assessment form
- See recent assessments
- Monitor high-risk patients

#### Dashboard Layout

```
┌─────────────────────────────────────────────────────────┐
│              DOCTOR DASHBOARD                           │
│           Dr. Sarah Ahmed's Clinic                      │
│                                                         │
│        Welcome, Dr. Sarah | [Logout]                   │
└─────────────────────────────────────────────────────────┘

QUICK STATISTICS CARDS
┌─────────────────────────────────────────────────────────┐
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │ TOTAL ASSESS │  │ HIGH-RISK    │  │TOTAL PATIENTS│ │
│  │     342      │  │ PATIENTS     │  │     178      │ │
│  │ (This Month) │  │ (Monitored)  │  │ (All-time)   │ │
│  │              │  │      28      │  │              │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                         │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │MONTHLY ASSESS│  │PENDING OUTC. │  │  AVG RISK    │ │
│  │      87      │  │ (To Record)  │  │   SCORE      │ │
│  │ (This Month) │  │       12     │  │    48.3      │ │
│  │              │  │              │  │  (Moderate)  │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                         │
└─────────────────────────────────────────────────────────┘

LEFT COLUMN: ASSESSMENT FORM
┌──────────────────────────┐
│  NEW PATIENT ASSESSMENT  │
│                          │
│ SELECT PATIENT:          │
│ ┌─ New Patient ─┐       │
│ ┌─ Returning ─┐         │
│                          │
│ DEMOGRAPHICS:            │
│ First Name: [_______]    │
│ Last Name:  [_______]    │
│ DOB:        [__/__/____] │
│ Gender:     [Male ▼]     │
│                          │
│ SYMPTOMS:                │
│ ☐ Sore throat:  [___]w   │
│ ☐ Voice changes         │
│ ☐ Difficulty swallowing │
│ ☐ Neck lump             │
│ ☐ Throat pain: [__]/10   │
│ ☐ Weight loss: [___]lbs  │
│                          │
│ LIFESTYLE:               │
│ Smoking: [Current ▼]     │
│ Years: [__]              │
│ Alcohol: [Moderate ▼]    │
│                          │
│ MEDICAL HISTORY:         │
│ ☐ HPV: [Unknown ▼]       │
│ ☐ Family history        │
│ ☐ Previous issues       │
│                          │
│ LAB VALUES:              │
│ Hemoglobin: [__.__]      │
│ Lymphocytes: [__]%       │
│ WBC: [__.__]             │
│                          │
│ NOTES:                   │
│ [____________________]   │
│ [____________________]   │
│                          │
│ [Submit] [Clear] [Draft] │
└──────────────────────────┘

RIGHT COLUMN: RECENT ASSESSMENTS TABLE
┌──────────────────────────────────────────┐
│        RECENT ASSESSMENTS (Last 10)      │
├─────┬──────────────┬──────┬────┬─────────┤
│Date │ Patient      │ Risk │Scr │ Outcome │
├─────┼──────────────┼──────┼────┼─────────┤
│Today│James Wilson  │🟠Mod │61  │⏳ Pending
├─────┼──────────────┼──────┼────┼─────────┤
│ 2/3 │Emily Rodrig. │🟢Low │28  │✅Record
├─────┼──────────────┼──────┼────┼─────────┤
│ 2/2 │Michael J.#2  │🔴High│71* │⏳Pending
├─────┼──────────────┼──────┼────┼─────────┤
│ 2/1 │Robert Chen   │🔴High│72  │✅Record
├─────┼──────────────┼──────┼────┼─────────┤
│1/31 │Jennifer M.   │🟢Low │35  │✅Record
├─────┼──────────────┼──────┼────┼─────────┤
│1/30 │Michael J.#1  │🟠Mod │52  │✅Record
└─────┴──────────────┴──────┴────┴─────────┘

Legend:
🟢 Low (0-39)      🟠 Moderate (40-64)   🔴 High (65+)
⏳ Pending outcome    ✅ Outcome recorded    *Adjusted
```

#### Daily Workflow

```
TIMELINE: February 4, 2026

09:15 - Patient 1: James Wilson (NEW)
├─ Dr. Sarah selects "New Patient"
├─ Fills form with all patient data
├─ Submits assessment
├─ System calculates: Risk 61 (MODERATE)
├─ Results page displays
├─ Dr. Sarah reviews findings
├─ Makes action: "URGENT specialist referral"
└─ Dashboard updated: James shows as "PENDING OUTCOME"

09:45 - Dr. Sarah checks Dashboard
├─ Sees new entry: James Wilson, 61 (🟠 Moderate), ⏳ Pending
├─ Quick stats updated (87 assessments this month)
└─ Continues with next patient

10:15 - Patient 2: Emily Rodriguez (NEW)
├─ Minimal symptoms, non-smoker
├─ Risk calculated: 28 (LOW)
├─ Dr. Sarah: "Viral, reassure, follow-up 2 months"
├─ Action: Benign/reassurance
└─ Dashboard: Emily marked "✅ RECORDED"

10:45 - Patient 3: Michael Johnson (FOLLOW-UP)
├─ Dr. Sarah selects "Returning Patient"
├─ Searches: "Michael"
├─ Selects from results (patient_id=155)
├─ Previous assessment shown: 52 (MODERATE, 6 months ago)
├─ Fills new clinical data
├─ Risk calculated: Base 56
├─ RiskComparisonEngine adjusts: +15 points → 71 (HIGH)
├─ Results show: Significant worsening
├─ Dr. Sarah: "This is concerning. STAT specialist referral"
└─ Dashboard: Michael shows as "52→71*" (adjusted)

[Lunch break]

14:30 - Patient 4: Robert Chen (OUTCOME RECORDING)
├─ Robert had HIGH risk (72) 1 week ago
├─ ENT specialist completed workup
├─ Results: MALIGNANT (squamous cell, stage 2B)
├─ Dr. Sarah records outcome:
│  ├─ final_diagnosis: Malignant
│  ├─ cancer_type: Squamous Cell Carcinoma
│  ├─ cancer_stage: 2B
│  ├─ treatment_plan: Surgery + Chemo
│  └─ follow_up_status: Treatment planned
├─ Outcome stored in patient_outcomes table
└─ Dashboard: Robert marked "✅ RECORDED"

17:00 - Daily Summary:
┌─────────────────────────────────────────┐
│ DAILY ASSESSMENT SUMMARY                │
├─────────────────────────────────────────┤
│ Total Assessments Today: 4              │
│ New Patients: 2 (James: 61, Emily: 28) │
│ Follow-ups: 2 (Michael: 52→71, Robert) │
│                                         │
│ Outcomes Recorded: 2                    │
│ Pending Outcomes: 2                     │
│                                         │
│ Actions Generated: 4                    │
│ • 2 URGENT referrals                    │
│ • 1 Routine reassurance                 │
│ • 1 Treatment planning                  │
│                                         │
│ Monthly Stats Updated:                  │
│ • 87 assessments (monthly)              │
│ • 342 assessments (YTD)                │
│ • 28 high-risk patients monitored       │
└─────────────────────────────────────────┘
```

---

### 7. Historical Data & Risk Comparison

**Files:**
- `models/RiskComparisonEngine.php`
- `models/HistoricalAnalytics.php`
- `models/PatientOutcome.php`

#### Overview

Sophisticated system for learning from patient history and population data:

**Within-Patient Trends:**
- Track symptom progression
- Monitor lab value changes
- Detect behavioral relapses
- Identify temporal patterns

**Population-Level Learning:**
- Find similar historical cases
- Analyze treatment effectiveness
- Calculate cohort malignancy rates
- Validate risk model accuracy

#### Case Study: Michael Johnson's Historical Comparison

**SCENARIO:**
Michael had first assessment 6 months ago (52-MODERATE). Now returning with concerning new symptoms.

**STEP 1: SYMPTOM PROGRESSION ANALYSIS**

```
Previous (Aug 2025):
├─ Sore throat: 4 weeks
├─ Voice changes: NO
├─ Difficulty swallowing: NO ← Key baseline
├─ Neck lump: NO
└─ Weight loss: None reported

Current (Feb 2026):
├─ Sore throat: 8 weeks (was 4) ← DOUBLED
├─ Voice changes: NO (unchanged)
├─ Difficulty swallowing: YES (was NO) ← CRITICAL NEW
├─ Neck lump: NO (unchanged)
└─ Weight loss: 5 lbs in 2 weeks

ANALYSIS:
├─ Symptom progression detected: YES
├─ Most concerning: New dysphagia
│  └─ Dysphagia = HIGH-RISK indicator for malignancy
├─ Duration persistence: 4w → 8w (doubled)
│  └─ Chronic issue (not transient)
└─ Weight loss: New finding
   └─ Systemic sign of disease

ADJUSTMENT FROM PROGRESSION: +12 points
├─ Dysphagia (new): +7
├─ Duration increase: +5
└─ Weight loss: +2 (capped)
```

**STEP 2: BEHAVIORAL CHANGE ANALYSIS**

```
Previous (Aug 2025):
├─ Smoking: Current smoker (25 pack-years)
└─ Alcohol: Moderate (3-4 drinks/week)

Current (Feb 2026):
├─ Smoking: Former smoker (QUIT!) ← EXCELLENT
└─ Alcohol: Mild (1-2 drinks/week) ← IMPROVEMENT

ANALYSIS:
├─ Smoking cessation: POSITIVE
│  ├─ No relapse (not a concerning reversal)
│  └─ Excellent health decision
├─ BUT: Symptoms worsened despite quitting
│  └─ Suggests symptoms NOT from recent smoking irritation
│  └─ Points toward malignancy vs. reactive inflammation
└─ Alcohol reduction: Also positive

CLINICAL SIGNIFICANCE:
If symptoms were smoking-related:
├─ Quitting would improve symptoms ✓
└─ Michael's symptoms worsened instead ✗
   └─ Suggests underlying disease (malignancy risk)

ADJUSTMENT FROM BEHAVIOR: -4 points
└─ Offset good behaviors: Both quit smoking & reduced alcohol
```

**STEP 3: LAB VALUE TREND ANALYSIS**

```
Previous (Aug 2025):
├─ Hemoglobin: 13.8 g/dL (normal-high)
├─ Lymphocytes: 20% (normal)
└─ WBC: Normal

Current (Feb 2026):
├─ Hemoglobin: 12.9 g/dL (low-normal)
├─ Lymphocytes: 23% (elevated)
└─ WBC: Normal

ANALYSIS:
Hemoglobin decline (13.8 → 12.9):
├─ Drop: 0.9 g/dL
├─ Interpretation: Gradual trend toward anemia
├─ Anemia can indicate:
│  ├─ Chronic blood loss (unlikely here)
│  ├─ Malignancy (possible)
│  └─ Bone marrow suppression (possible)
├─ Concerning in context of symptoms
└─ Adjustment: +3 points

Lymphocyte elevation (20% → 23%):
├─ Within normal range but elevated
├─ Interpretation: Immune response to stimuli
├─ Could indicate:
│  ├─ Viral infection (less likely, would resolve)
│  ├─ Cancer-triggered immune response (possible)
│  └─ Chronic inflammation (likely)
└─ Adjustment: +2 points

ADJUSTMENT FROM LABS: +5 points
└─ Total: +3 (hemoglobin) + 2 (lymphocytes)
```

**STEP 4: TEMPORAL PATTERN ANALYSIS**

```
Previous assessment: August 22, 2025
Current assessment: February 4, 2026
Days between: 166 days (about 6 months)

ANALYSIS:
├─ Assessment frequency: Every 166 days
├─ Interval: Routine follow-up (not urgent)
├─ Clinical significance:
│  ├─ NOT a new emergency evaluation
│  ├─ Regular monitoring protocol
│  └─ But symptoms persisted entire 6 months
├─ 6 months of unresolved symptoms: Concerning
│  └─ Suggests chronic pathology
└─ Adjustment: 0 points (routine timing)

INTERPRETATION:
The 6-month duration combined with worsening trajectory
is significant for potential malignancy.
```

**STEP 5: CALCULATE TOTAL ADJUSTMENT**

```
Base score (current clinical data): 56

Adjustments:
├─ Score trajectory (minor worsening): +2
├─ Symptom progression (new dysphagia): +12
├─ Behavioral changes (quit smoking): -4
├─ Lab decline (anemia trend): +5
└─ Temporal pattern (routine): 0
────────────────────────────────────
TOTAL ADJUSTMENT: +15 points

Final Risk Score: 56 + 15 = 71 (HIGH RISK!)

Confidence Adjustment:
├─ Base confidence: 76%
├─ Multiple factors point same direction: +10%
├─ Symptom consistency: +5%
├─ Lab support: +3%
└─ Final Confidence: 94%
```

**STEP 6: GENERATE INSIGHTS**

```
System generates detailed text insights:

"⬆️ WORSENING TREND: Patient condition shows progressive 
  deterioration. Multiple risk factors increasing.

🔴 NEW SYMPTOMS DETECTED: Difficulty swallowing (NEW, 
  HIGH CONCERN) - major red flag for laryngeal malignancy

📈 WORSENING SYMPTOMS: Sore throat duration increased from 
  4 weeks to 8 weeks over 6 months

✅ POSITIVE BEHAVIORAL CHANGES: Patient has quit smoking 
  and reduced alcohol consumption. However, worsening 
  symptoms despite these positive changes suggests 
  underlying disease rather than lifestyle-related issues.

🩸 LAB VALUES: Hemoglobin gradually declining toward 
  anemia (13.8 → 12.9 g/dL), suggesting possible chronic 
  disease process.

⏰ TEMPORAL PATTERN: 6-month duration with persistent and 
  progressive symptoms is concerning for chronic 
  malignancy.

📋 CLINICAL SUMMARY: Base score of 56 adjusted upward to 
  71 based on historical context and symptom progression. 
  The emergence of difficulty swallowing is particularly 
  concerning and represents a significant clinical 
  development. Recommend proceeding with HIGH urgency."
```

---

### 8. Population Learning from Historical Outcomes

**Files:**
- `models/HistoricalAnalytics.php`
- `database/migration_patient_outcomes.sql`

#### Overview

System learns from accumulated outcomes to provide population-level context:

**Three Key Analytics:**

1. **Similar Case Finder** - Find patients with matching profiles
2. **Cohort Statistics** - Show malignancy rates for similar groups
3. **Treatment Effectiveness** - Track which treatments work best

#### Case Study: Using Population Data for New Patient (David Martinez)

**SCENARIO:**
David Martinez, 55-year-old current smoker, presents with moderate risk (58). System has 6 months of outcome data.

**STEP 1: FIND SIMILAR CASES**

```
Query: Find patients matching David's profile
├─ Age: 45-65 years (±10) ← David is 55 ✓
├─ Smoking: Current ✓
├─ Risk score: 48-68 (±10) ← David scored 58 ✓
└─ Must have outcome data ✓

Database Search Results:
Found: 6 similar historical cases

Case 1: Robert Chen
├─ Age: 58, Current smoker, Risk score: 62
├─ Outcome: MALIGNANT (Stage 2A)
├─ Treatment: Surgery + Adjuvant chemo
├─ Timeline: 38 days to diagnosis
└─ Follow-up: Ongoing treatment

Case 2: Thomas Davis
├─ Age: 52, Current smoker, Risk score: 55
├─ Outcome: BENIGN (Chronic pharyngitis)
├─ Treatment: Monitoring only
├─ Timeline: 42 days to diagnosis
└─ Follow-up: Resolved with rest

Case 3: Antonio Ramirez
├─ Age: 59, Current smoker, Risk score: 60
├─ Outcome: MALIGNANT (Stage 1)
├─ Treatment: Surgery (TLM - transoral laser microsurgery)
├─ Timeline: 35 days to diagnosis
└─ Follow-up: NED (No Evidence of Disease) at 6 months

Case 4: Marcus Johnson
├─ Age: 54, Current smoker, Risk score: 58
├─ Outcome: BENIGN (Laryngeal papillomatosis)
├─ Treatment: Monitoring + periodic debridement
├─ Timeline: 48 days to diagnosis
└─ Follow-up: Stable on monitoring

Case 5: George Williams
├─ Age: 60, Current smoker, Risk score: 65
├─ Outcome: MALIGNANT (Stage 3)
├─ Treatment: Chemo + Radiation
├─ Timeline: 40 days to diagnosis
└─ Follow-up: Partial response, ongoing treatment

Case 6: David Sr. Taylor
├─ Age: 56, Current smoker, Risk score: 56
├─ Outcome: BENIGN (Reflux laryngitis)
├─ Treatment: PPI medication + voice rest
├─ Timeline: 32 days to diagnosis
└─ Follow-up: Resolved in 8 weeks

FINDINGS:
├─ Total similar cases: 6
├─ Malignancy rate: 3 out of 6 (50%)
│  ├─ Robert: Malignant (Stage 2A)
│  ├─ Antonio: Malignant (Stage 1)
│  └─ George: Malignant (Stage 3)
├─ Benign rate: 3 out of 6 (50%)
│  ├─ Thomas: Benign
│  ├─ Marcus: Benign
│  └─ David Sr: Benign
└─ Average timeline to diagnosis: 39 days
```

**STEP 2: COHORT STATISTICS**

```
Query: Current smokers with MODERATE risk (David's cohort)

Results:
├─ Total in cohort: 8 patients
├─ Average age: 54 years (David: 55) ✓ Perfect match
├─ Average risk score: 56 (David: 58) ✓ Close match
│
├─ With complete outcome data: 4 patients
│  ├─ Malignant: 2 (50%)
│  ├─ Benign: 2 (50%)
│  └─ Still pending: 4 patients
│
├─ Demographics breakdown:
│  ├─ Age 40-50: 3 patients (1 malignant, 2 benign)
│  ├─ Age 50-60: 4 patients (2 malignant, 2 benign) ← David's group
│  └─ Age 60+: 1 patient (0 malignant, 1 benign)
│
├─ Timeline to diagnosis:
│  ├─ Average: 38 days
│  ├─ Range: 32-48 days
│  └─ Fastest: David Sr (32 days)
│
└─ Survival metrics (malignant cases only):
   ├─ Cases followed: 2 (Robert and Antonio)
   ├─ NED (cancer-free): 1 out of 2 (50%)
   └─ Average follow-up: 6 months
```

**STEP 3: TREATMENT EFFECTIVENESS**

```
Query: For patients diagnosed with malignancy in cohort,
what was treatment success?

Malignant Cases in Database (n=3):
├─ Robert Chen:
│  ├─ Treatment: Surgery + Adjuvant chemo
│  ├─ Stage: 2A
│  ├─ Status: 6 months post-op
│  ├─ Outcome: NED (No Evidence of Disease)
│  └─ Success: ✅ YES
│
├─ Antonio Ramirez:
│  ├─ Treatment: Surgery only (TLM)
│  ├─ Stage: 1 (early)
│  ├─ Status: 6 months post-op
│  ├─ Outcome: NED
│  └─ Success: ✅ YES
│
└─ George Williams:
   ├─ Treatment: Chemo + Radiation
   ├─ Stage: 3 (advanced)
   ├─ Status: Currently in treatment
   ├─ Outcome: Partial response
   └─ Success: ⏳ Pending (too early)

TREATMENT SUMMARY:
├─ Surgery (2 cases):
│  ├─ Success rate: 2/2 (100%)
│  ├─ Both NED
│  └─ Outcome: Excellent
│
└─ Chemotherapy + Radiation (1 case):
   ├─ Success rate: Partial response (ongoing)
   ├─ Advanced stage (harder to treat)
   └─ Outcome: Too early to assess

CONCLUSION:
Surgery had excellent outcomes (100% so far).
This information can guide treatment discussions if
malignancy is confirmed.
```

**STEP 4: GENERATE RECOMMENDATION**

```
System Analysis:
├─ Similar cases found: 6
├─ Malignancy rate in similar: 50%
├─ Treatment success (if malignancy): 100% (surgery)
└─ Average diagnostic timeline: 39 days

Generated Recommendation:

"Historical Data Context (Moderate Confidence):

Based on analysis of 6 patients with similar risk profiles
in our database:

KEY FINDINGS:
├─ Malignancy Rate: 50% of similar patients were diagnosed
│  with malignancy (3 out of 6 cases)
├─ This aligns with your current risk assessment of 58
│  (MODERATE), suggesting approximately 1 in 2 chance
│  of actual cancer
│
├─ Timeline: Average time from assessment to diagnosis
│  was 39 days in similar cases (range: 32-48 days)
│  This helps set patient expectations for workup
│
└─ Treatment Outcomes: Surgery was most effective in
   similar cases with 100% success rate (both patients
   NED - No Evidence of Disease). If malignancy is
   found, surgery should be strongly considered.

RECOMMENDATION:
Given 50% malignancy rate in similar cohort and your
moderate risk score, urgent evaluation is justified.
Recommend expedited ENT referral with imaging and
consideration of biopsy if any lesions found.

CONFIDENCE LEVEL: MODERATE-HIGH
(Based on 6 similar cases with documented outcomes)"
```

---

## Admin Features

**File:** `views/admin_dashboard.php`

### Overview

Administrative dashboard for system monitoring and management:

```
┌──────────────────────────────────────────────────────┐
│           ADMIN DASHBOARD                            │
│      System Analytics & Management                   │
│                                                      │
│       Welcome, Admin | [Logout]                     │
└──────────────────────────────────────────────────────┘

SYSTEM STATISTICS
┌──────────────────────────────────────────────────────┐
│ Total Assessments: 342                              │
│ Total Patients: 178                                 │
│ Total Outcomes Recorded: 23                         │
│ High-Risk Patients: 28                              │
│ Average Risk Score: 48.3                            │
│ Success Rate (Benign): 65%                          │
│ Malignancy Detection: 35% (8 out of 23)            │
└──────────────────────────────────────────────────────┘

RISK DISTRIBUTION TABLE
┌──────────────────────────────────────────────────────┐
│ Risk Level    │ Count  │ Percentage │ Status       │
├───────────────┼────────┼────────────┼──────────────┤
│ 🟢 Low        │ 145    │ 42.4%      │ Monitoring   │
├───────────────┼────────┼────────────┼──────────────┤
│ 🟠 Moderate   │ 169    │ 49.4%      │ Follow-up    │
├───────────────┼────────┼────────────┼──────────────┤
│ 🔴 High       │ 28     │ 8.2%       │ Urgent       │
└──────────────────────────────────────────────────────┘

HIGH-RISK PATIENTS ALERT LIST
┌──────────────────────────────────────────────────────┐
│ Name           │ Score │ Status    │ Days Pending │
├────────────────┼───────┼───────────┼──────────────┤
│ Robert Chen    │ 72    │ Recording │ 7 days       │
├────────────────┼───────┼───────────┼──────────────┤
│ Michael J.     │ 71    │ Pending   │ NEW          │
├────────────────┼───────┼───────────┼──────────────┤
│ James Wilson   │ 61    │ Pending   │ NEW          │
├────────────────┼───────┼───────────┼──────────────┤
│ (more...)      │       │           │              │
└──────────────────────────────────────────────────────┘

DOCTOR MANAGEMENT
┌──────────────────────────────────────────────────────┐
│ Doctor         │ Assessments │ High-Risk │ Status    │
├────────────────┼─────────────┼───────────┼───────────┤
│ Dr. Sarah      │ 87          │ 8         │ Active    │
├────────────────┼─────────────┼───────────┼───────────┤
│ Dr. Michael    │ 65          │ 5         │ Active    │
├────────────────┼─────────────┼───────────┼───────────┤
│ Dr. Patricia   │ 190         │ 15        │ Active    │
└──────────────────────────────────────────────────────┘

ACTIVITY LOG
┌──────────────────────────────────────────────────────┐
│ Timestamp          │ Doctor    │ Action              │
├────────────────────┼───────────┼─────────────────────┤
│ 2026-02-04 14:30   │ Dr. Sarah │ Recorded outcome    │
├────────────────────┼───────────┼─────────────────────┤
│ 2026-02-04 14:15   │ Dr. Sarah │ Created assessment  │
├────────────────────┼───────────┼─────────────────────┤
│ 2026-02-04 13:45   │ Dr. Sarah │ Referred patient    │
└──────────────────────────────────────────────────────┘
```

---

## Database Schema

### Core Tables

```sql
users (User accounts and authentication)
├─ id (INT, PK, auto-increment)
├─ username (VARCHAR, UNIQUE)
├─ email (VARCHAR)
├─ password_hash (VARCHAR, bcrypt)
├─ full_name (VARCHAR)
├─ specialization (VARCHAR)
├─ license_number (VARCHAR)
├─ role (ENUM: doctor, admin)
├─ is_active (BOOLEAN)
└─ timestamps

patients (Patient demographics)
├─ id (INT, PK, auto-increment)
├─ first_name (VARCHAR)
├─ last_name (VARCHAR)
├─ date_of_birth (DATE)
├─ gender (ENUM: M, F, Other)
├─ contact_phone (VARCHAR)
├─ contact_email (VARCHAR)
├─ medical_record_number (VARCHAR, UNIQUE)
└─ timestamps

assessments (Risk assessments)
├─ id (INT, PK, auto-increment)
├─ patient_id (INT, FK → patients)
├─ doctor_id (INT, FK → users)
├─ [40+ clinical data fields]
├─ assessment_date (DATE)
└─ timestamps

risk_results (Risk scores and results)
├─ id (INT, PK, auto-increment)
├─ assessment_id (INT, FK → assessments, UNIQUE)
├─ risk_score (DECIMAL 0-100)
├─ risk_level (ENUM: Low, Moderate, High)
├─ confidence_percentage (DECIMAL 0-100)
├─ primary_factors (TEXT)
├─ secondary_factors (TEXT)
├─ clinical_recommendation (TEXT)
├─ trend_direction (ENUM)
├─ adjustment_factor (DECIMAL)
├─ comparison_data (JSON)
└─ timestamps

patient_outcomes (Diagnosis and treatment)
├─ id (INT, PK, auto-increment)
├─ patient_id (INT, FK → patients)
├─ assessment_id (INT, FK → assessments, UNIQUE)
├─ final_diagnosis (ENUM: Malignant, Benign, Pending, Unknown)
├─ cancer_stage (VARCHAR)
├─ cancer_type (VARCHAR)
├─ treatment_plan (TEXT)
├─ treatment_urgency (VARCHAR)
├─ clinical_findings (TEXT)
├─ recommendations (TEXT)
├─ follow_up_date (DATE)
├─ tumor_location (VARCHAR)
├─ outcome_date (DATE)
├─ follow_up_status (ENUM: NED, Recurrence, Progressive)
├─ survival_status (ENUM: Alive, Deceased)
├─ years_survived (DECIMAL)
├─ notes (TEXT)
└─ timestamps

system_logs (Audit trail)
├─ id (INT, PK, auto-increment)
├─ user_id (INT, FK → users)
├─ action (VARCHAR)
├─ resource_type (VARCHAR)
├─ resource_id (INT)
├─ description (TEXT)
└─ created_at (TIMESTAMP)
```

---

## Data Flow

```
USER LOGIN
├─ AuthController::login()
├─ User::authenticate()
├─ Session created
└─ Redirect to dashboard

NEW ASSESSMENT (New Patient)
├─ PatientController::searchPatientAPI() (if returning patient)
├─ DoctorController handles form
├─ Patient::createPatient() (if new)
├─ Assessment::createAssessment()
├─ RiskScoringEngine::calculateRisk()
├─ RiskComparisonEngine::analyzeWithHistory() (if has history)
├─ RiskResult stored
└─ Display results page

OUTCOME RECORDING
├─ ActionController::recordOutcome()
├─ PatientOutcome::recordOutcome()
├─ Store diagnosis, treatment, follow-up
└─ Enable historical learning

HISTORICAL ANALYSIS
├─ Assessment submitted
├─ RiskComparisonEngine compares against previous
├─ HistoricalAnalytics generates population insights
└─ Results displayed with historical context
```

---

## Security & Compliance

### HIPAA Compliance Features

```
✅ Audit Logging
├─ All actions logged to system_logs
├─ WHO: user_id
├─ WHAT: action taken
├─ WHEN: timestamp
├─ WHERE: IP address (future)
└─ Resource tracking: resource_type, resource_id

✅ Access Control
├─ Role-based (Doctor/Admin)
├─ Session-based authentication
├─ Password hashing (bcrypt)
└─ Session timeout (30 minutes)

✅ Data Protection
├─ Passwords never stored plaintext
├─ Password_verify() for comparison
├─ Prepared statements (SQL injection prevention)
├─ Input sanitization
└─ Output escaping (HTML entities)

✅ Database Security
├─ Foreign key constraints
├─ NOT NULL constraints
├─ UNIQUE constraints (username, MRN, etc)
└─ Indexes for performance
```

### SQL Injection Prevention

```php
// ❌ UNSAFE (vulnerable):
$result = $db->query("SELECT * FROM users WHERE username = '" . $_POST['username'] . "'");

// ✅ SAFE (using prepared statements):
$stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$_POST['username']]);
$result = $stmt->fetch();
```

### Password Security

```php
// Storing password:
$hash = password_hash($password, PASSWORD_BCRYPT);
// Example: $2y$10$ZjF3kL9nQxRmL2pW9vH8hOzK7dF4bN3mQ5sT6uV7wX8yZ9aB1cD2e

// Verifying password:
if (password_verify($inputPassword, $storedHash)) {
    // Password matches!
} else {
    // Invalid password
}
```

---

## User Workflows

### Workflow 1: Initial Assessment (New Patient)

```
DOCTOR
├─ Logs in
├─ Views dashboard
├─ Clicks "New Patient Assessment"
├─ Fills demographics (first name, DOB, etc)
├─ Fills clinical form (symptoms, lifestyle, labs)
├─ Reviews data for accuracy
├─ Clicks "Submit Assessment"
└─ System processes

SYSTEM
├─ Creates new patient record
├─ Creates assessment record
├─ Calculates base risk score
├─ Determines risk level
├─ Calculates confidence
├─ Stores risk result
└─ Displays results page

DOCTOR
├─ Reviews risk score and factors
├─ Reads clinical recommendation
├─ Makes action decision:
│  ├─ Refer to specialist (if high/moderate)
│  └─ Reassure and monitor (if low)
├─ Records action with follow-up date
└─ Patient sent to specialist or home
```

### Workflow 2: Follow-up Assessment (Returning Patient)

```
DOCTOR
├─ Logs in
├─ Clicks "Returning Patient Assessment"
├─ Types patient name in search
├─ Views search results
├─ Selects patient from list
├─ Views previous assessments in history panel
├─ Demographics auto-filled (read-only)
├─ Enters NEW clinical data only
├─ Reviews for accuracy
└─ Clicks "Submit Assessment"

SYSTEM
├─ Fetches previous assessments for patient
├─ Calculates new base risk score
├─ Compares against previous score
├─ Analyzes symptom progression
├─ Analyzes behavioral changes
├─ Analyzes lab trends
├─ Calculates adjustment (±30 points)
├─ Produces adjusted risk score
├─ Increases confidence (data consistency)
├─ Generates insights text
└─ Displays results with comparison

DOCTOR
├─ Reviews base score and adjustment
├─ Sees trend visualization (score trajectory)
├─ Reads detailed insights (worsening/improving/stable)
├─ Makes action decision based on trend
└─ Records action with follow-up
```

### Workflow 3: Outcome Recording

```
DOCTOR
├─ Patient returns with specialist report
├─ Opens patient's assessment record
├─ Clicks "Record Outcome"
├─ Fills outcome form:
│  ├─ Final diagnosis (Malignant/Benign/Other)
│  ├─ Cancer type (if malignant)
│  ├─ Cancer stage (if malignant)
│  ├─ Treatment plan
│  ├─ Follow-up status
│  └─ Clinical notes
└─ Submits outcome

SYSTEM
├─ Stores outcome in patient_outcomes table
├─ Links to assessment record
├─ Outcome now available for historical analysis
└─ Future patients with similar profile see:
   ├─ This patient's case in similar cases list
   ├─ Outcome data in cohort statistics
   └─ Treatment data in effectiveness analysis
```

---

## Setup & Deployment

### Requirements

```
Server:
├─ PHP 7.4+
├─ MySQL 5.7+ (or MariaDB 10.3+)
└─ Apache with mod_rewrite

PHP Extensions:
├─ PDO
├─ PDO_MySQL
└─ OpenSSL (for password hashing)

Client:
└─ Modern web browser (Chrome, Firefox, Safari, Edge)
```

### Installation Steps

```bash
# 1. Clone repository to web root
cd /var/www/html
git clone [repo-url] CANCER

# 2. Create database
mysql -u root -p < CANCER/database/schema.sql

# 3. Configure database connection
# Edit config/db_config.php with credentials

# 4. Load demo data (optional)
mysql -u root -p CANCER_DB < CANCER/database/DEMO_USERS.sql

# 5. Set permissions
chmod 755 CANCER/
chmod 644 CANCER/*.php

# 6. Access system
# Navigate to: http://localhost/CANCER/
# Login with demo credentials
```

### Configuration

```php
// config/db_config.php
define('DB_HOST', 'localhost');
define('DB_USER', 'cancer_user');
define('DB_PASS', 'secure_password');
define('DB_NAME', 'cancer_db');

$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
// PDO connection with error handling
```

---

## API Endpoints

### Patient Search API

```php
Endpoint: index.php?page=api-patient-search
Method: POST
Input: {
    search_term: string (min 2 chars)
}
Output: {
    "success": boolean,
    "data": [
        {
            "id": integer,
            "first_name": string,
            "last_name": string,
            "age": integer,
            "medical_record_number": string,
            "last_assessment": date
        }
    ]
}
```

### Patient Assessments API

```php
Endpoint: index.php?page=api-patient-assessments
Method: POST
Input: {
    patient_id: integer
}
Output: {
    "success": boolean,
    "data": [
        {
            "id": integer,
            "assessment_date": date,
            "risk_score": decimal,
            "risk_level": string,
            "outcome_status": string
        }
    ]
}
```

### Historical Insights API

```php
Endpoint: index.php?page=api-historical-insights
Method: GET
Parameters: assessment_id=integer
Output: {
    "success": boolean,
    "data": {
        "similar_cases": array,
        "diagnosis_distribution": array,
        "treatment_effectiveness": array,
        "cohort_stats": object,
        "risk_accuracy": object,
        "recommendation": object
    }
}
```

---

## Troubleshooting

### Common Issues

```
ISSUE: "Database connection failed"
SOLUTION:
├─ Check credentials in config/db_config.php
├─ Verify MySQL server is running
└─ Confirm database exists

ISSUE: "Session expired"
SOLUTION:
├─ Login again (30-minute timeout is normal)
├─ Check server date/time
└─ Clear browser cookies if persistent

ISSUE: "Cannot find patient"
SOLUTION:
├─ Check search term has minimum 2 characters
├─ Verify patient exists in database
└─ Try searching by MRN instead of name

ISSUE: "Risk score not calculated"
SOLUTION:
├─ Ensure all required fields are filled
├─ Check form validation errors
└─ Verify assessment data was saved
```

---

## Future Enhancements (Phase 3-4)

### Phase 3: Full Backend Integration
```
├─ Real-time data validation
├─ Advanced search filters
├─ Bulk import functionality
└─ Report generation
```

### Phase 4: ML Risk Engine
```
├─ Machine learning model for risk scoring
├─ Automated model training from outcomes
├─ Confidence calibration
└─ Population-specific risk models
```

---

## Support & Contact

For questions or issues:
- Documentation: See README.md and ARCHITECTURE.txt
- Issues: Check system_logs table for audit trail
- Admin support: Contact system administrator

---

**End of Documentation**

*Version 1.0 | February 4, 2026*
