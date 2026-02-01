# ICDSS - Intelligent Clinical Decision Support System
## Throat Cancer Risk Assessment Platform

**Phase 1: Frontend & Database Design**

---

## 📋 PROJECT OVERVIEW

The ICDSS is a PHP-based clinical decision support system designed to assist doctors in early throat cancer risk assessment. This Phase 1 implementation focuses on:

- ✅ Hospital-grade UI/UX design
- ✅ MySQL database schema
- ✅ Frontend templates (HTML/CSS/JS)
- ✅ Backend PHP structure and models
- ✅ User authentication system
- ⏳ Placeholder for Phase 4 risk engine

---

## 🏗️ SYSTEM ARCHITECTURE

### Directory Structure

```
/CANCER/
├── /config/               # Configuration files
│   └── db_config.php      # Database connection & utilities
├── /controllers/          # Business logic controllers
│   ├── AuthController.php       # User authentication
│   ├── DoctorController.php     # Doctor dashboard
│   ├── AdminController.php      # Admin dashboard
│   ├── AssessmentController.php # Risk assessment operations
│   ├── PatientController.php    # Patient management
│   └── ActionController.php     # Clinical actions
├── /models/               # Data models
│   ├── User.php          # User authentication & management
│   ├── Patient.php       # Patient data
│   ├── Assessment.php    # Assessment/symptom collection
│   └── RiskResult.php    # Risk scoring results
├── /views/               # HTML templates
│   ├── login.php         # Login page
│   ├── doctor_dashboard.php    # Doctor main interface
│   ├── assessment_results.php  # Risk result display
│   └── admin_dashboard.php     # Admin interface
├── /assets/              # Static resources
│   ├── /css/
│   │   └── style.css     # Main stylesheet
│   └── /js/
│       ├── form_validation.js  # Client-side validation
│       ├── dashboard.js        # Doctor dashboard utilities
│       └── admin.js            # Admin utilities
├── /database/
│   └── schema.sql        # MySQL database schema
└── index.php            # Main application router
```

---

## 🗄️ DATABASE SCHEMA

### Tables Created

1. **users** - Doctor and admin accounts
   - Supports multiple roles and specializations
   - Password hashing with bcrypt

2. **patients** - Patient demographics
   - Medical record number (MRN)
   - Basic contact information

3. **assessments** - Risk assessment data
   - Symptoms (sore throat, voice changes, etc.)
   - Lifestyle factors (smoking, alcohol)
   - Medical history (HPV, family history)
   - Lab indicators (hemoglobin, lymphocyte count)

4. **risk_results** - Assessment outcomes
   - Risk score (0-100)
   - Risk level (Low/Moderate/High)
   - Confidence percentage
   - Primary & secondary factors
   - Clinical recommendations

5. **action_history** - Clinical decisions
   - Action type (referred, monitoring, etc.)
   - Follow-up dates
   - Clinical notes

6. **system_logs** - Audit trail
   - User actions
   - Resource modifications
   - Compliance tracking

---

## 🔐 AUTHENTICATION

### User Roles

**Doctor**
- Create patient assessments
- View assessment results
- Record clinical actions
- Access own patient history

**Admin**
- View all assessments
- Manage user accounts
- System statistics
- Risk model configuration (Phase 4)

### Demo Credentials (Setup Needed)

```sql
-- Doctor account
Username: doctor1
Password: password123

-- Admin account  
Username: admin1
Password: admin123
```

---

## 🎨 FRONTEND DESIGN

### Design Philosophy

- **Hospital-Grade**: Clean, professional aesthetic appropriate for clinical environment
- **Accessibility**: High contrast, large text, clear labels
- **Responsiveness**: Works on desktop, tablet, mobile
- **No Animations**: Functional over decorative

### Key Interfaces

#### 1. Doctor Dashboard

**Assessment Form Section**
- Patient search with autocomplete
- Demographics (age, gender)
- Symptoms checklist (sore throat, voice changes, etc.)
- Lifestyle factors (smoking, alcohol)
- Medical history
- Lab indicators
- Form validation and error handling

**Risk Results Section**
- Risk score visualization (0-100 scale)
- Risk level badge (Low/Moderate/High)
- Confidence indicator (progress bar)
- Clinical recommendations
- Key risk factors (primary & secondary)
- Detailed assessment breakdown
- Clinical action recording

**Patient History Table**
- Recent assessments
- Risk levels
- Trends
- Quick access to details

#### 2. Admin Dashboard

**System Statistics**
- Total assessments
- Active doctors
- Registered patients
- High-risk cases

**Risk Distribution**
- Breakdown by risk level
- Average scores
- Trending data

**High-Risk Patients**
- List requiring attention
- Quick review access

**Doctor Management**
- Active doctors
- Assessment counts
- License verification
- Status tracking

**System Health**
- Connection status
- Activity logs
- Configuration placeholders

#### 3. Login Page

- Professional header with system name
- Demo credentials display
- Clear form labels
- Error messages

---

## 📊 ASSESSMENT FORM FIELDS

### Patient Demographics
- Age (18-120)
- Gender (Male/Female/Other)

### Symptoms (Checkbox/Duration)
- Persistent sore throat (weeks)
- Voice changes (hoarseness)
- Difficulty swallowing
- Neck lump (palpable mass)
- Unexplained weight loss (% and duration)

### Lifestyle Factors
- Smoking status (never/former/current)
- Smoking duration (years)
- Alcohol consumption (none/mild/moderate/heavy)

### Medical History
- HPV status (positive/negative/unknown)
- Family cancer history
- Cancer types in family

### Lab Indicators
- Hemoglobin level (g/dL, normal: 13.5-17.5)
- Lymphocyte count (K/uL, normal: 1.0-4.8)

### Clinical Notes
- Free-text field for additional observations

---

## 🚀 SETUP INSTRUCTIONS

### Prerequisites
- XAMPP or similar (Apache + MySQL + PHP 7.4+)
- Web browser

### Installation Steps

#### 1. Create Database

```bash
# Open MySQL console or phpMyAdmin
# Create database
CREATE DATABASE icdss_cancer_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 2. Import Schema

```bash
# Import the schema.sql file
mysql -u root icdss_cancer_db < database/schema.sql
```

#### 3. Create Demo Users

```sql
-- Insert demo doctor account
INSERT INTO users (username, email, password_hash, full_name, role, specialization, license_number)
VALUES (
    'doctor1',
    'doctor1@icdss.local',
    '$2y$10$...',  -- bcrypt hash of 'password123'
    'Dr. Sarah Johnson',
    'doctor',
    'Oncology',
    'LIC12345'
);

-- Insert demo admin account
INSERT INTO users (username, email, password_hash, full_name, role)
VALUES (
    'admin1',
    'admin1@icdss.local',
    '$2y$10$...',  -- bcrypt hash of 'admin123'
    'Admin User',
    'admin'
);
```

To generate bcrypt hashes, use:
```php
<?php
echo password_hash('password123', PASSWORD_DEFAULT);
?>
```

#### 4. Configure Database Connection

Edit `/config/db_config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');  // Update if needed
define('DB_NAME', 'icdss_cancer_db');
```

#### 5. Access Application

1. Place the `/CANCER` folder in `C:\xampp\htdocs\`
2. Open browser: `http://localhost/CANCER/`
3. Login with demo credentials

---

## 🔧 DEVELOPMENT NOTES

### Phase 1 Status (Current)

✅ **Completed:**
- Database schema design
- User authentication system
- Doctor dashboard UI
- Admin dashboard UI
- Assessment form with validation
- Results display template
- CSS styling (hospital-grade)
- JavaScript form utilities
- PHP models and controllers
- Database configuration

⏳ **Phase 2 - Pending:**
- Real data storage implementation
- Complete patient search AJAX
- Form submission handlers
- Real-time statistics

⏳ **Phase 3 - Pending:**
- Backend form processing
- Database integration
- Authentication verification
- Patient management API
- Real statistics calculation

⏳ **Phase 4 - Pending:**
- **Risk Scoring Engine** - Weighted scoring algorithm
  - Core variables (symptoms, HPV status)
  - Secondary variables (lifestyle, medical history)
  - Threshold-based classification
  - Explainability tracking

### Risk Engine Placeholder

The risk engine (Phase 4) will replace the placeholder in `RiskResult::createPlaceholderResult()`:

```php
// TODO: Phase 4 - Implement actual risk scoring logic
// Replace dummy values with:
// - Feature extraction from assessment
// - Weight calculation from trained model
// - Threshold-based classification
// - Confidence scoring
```

---

## 📝 API ENDPOINTS (Phase 3+)

### Authentication
- `POST /auth.php` - Login
- `GET /logout.php` - Logout

### Assessments
- `POST /controllers/AssessmentController.php` - Create assessment
- `GET /index.php?page=assessment-results&id={id}` - View results

### Patients
- `GET /controllers/PatientController.php?action=search&term={term}` - Search patients

### Clinical Actions
- `POST /controllers/ActionController.php` - Record clinical decision

---

## 🎯 VALIDATION RULES

### Client-Side (JavaScript)
- Age: 18-120 years
- Smoking duration: 0-80 years
- Weight loss: 0-100%
- Hemoglobin: 5-20 g/dL
- Lymphocyte: 0-10 K/uL

### Server-Side (PHP)
- All inputs sanitized (htmlspecialchars)
- Prepared statements for SQL injection prevention
- Type validation and casting
- Range validation for medical values

---

## 🔒 SECURITY FEATURES

- **Password Hashing**: bcrypt with PASSWORD_DEFAULT
- **SQL Injection Prevention**: Prepared statements (PDO)
- **XSS Prevention**: htmlspecialchars() for all user input
- **Session Management**: PHP session variables
- **Audit Trail**: All actions logged to system_logs
- **Role-Based Access Control**: Doctor vs Admin routes

---

## 📦 DEPENDENCIES

**Backend:**
- PHP 7.4+ (PDO MySQL)
- MySQL 5.7+ or MariaDB 10.3+

**Frontend:**
- Modern browser (ES6 support)
- No external libraries (pure HTML/CSS/JS)

---

## 🚨 IMPORTANT NOTES

### Disclaimer
This system provides **decision support only**, not diagnosis. All assessments should be reviewed by qualified medical professionals.

### Data Privacy
- All patient data must be handled according to HIPAA/GDPR
- Implement proper access controls
- Secure database backups
- Audit logging enabled by default

### For Production Deployment
1. Use HTTPS/TLS encryption
2. Implement database user permissions
3. Use environment variables for credentials
4. Enable audit logging
5. Implement rate limiting
6. Add CSRF protection tokens
7. Regular security audits

---

## 📞 SUPPORT

For questions about implementation:
- Review inline code comments
- Check database schema for field descriptions
- Consult medical literature for assessment criteria

---

## 📄 LICENSE

This is a reference implementation for educational purposes.

---

**Last Updated:** January 2026
**Phase:** 1 (Frontend & Database Design)
**Status:** Ready for Phase 2 (Backend Implementation)
