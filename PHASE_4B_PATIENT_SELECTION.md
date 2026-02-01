# Phase 4B: Patient Selection UI & Incremental Data Management
**Implementation Date:** February 1, 2024  
**Status:** ✅ COMPLETE & DEPLOYED  
**GitHub Commit:** cc6b3d3

## Overview

Phase 4B addresses a critical workflow issue: the system was creating duplicate patient records whenever a patient came back for follow-up assessments. This prevented the Risk Comparison Engine from functioning properly because it couldn't find historical assessments (they were scattered across multiple patient IDs).

**Solution:** Implement a patient selection interface that allows doctors to:
1. Identify whether a patient is NEW or RETURNING for follow-up
2. For returning patients: search by name/MRN and review previous assessments
3. For new patients: proceed directly to assessment form
4. Attach new assessments to existing patient record (incremental data) rather than creating duplicates

## Architecture

### Three-Stage Patient Flow

```
Stage 1: Patient Selection
   ├─ "New Patient" button → Stage 3 (Form Only)
   └─ "Returning Patient" button → Stage 2 (Search)

Stage 2: Patient Search & History
   ├─ AJAX search by name or MRN
   ├─ Display matching patients with last assessment date
   ├─ Show patient demographics (DOB, age, gender)
   ├─ Display all previous assessments
   │  ├─ Assessment #N (newest first)
   │  ├─ Date, Risk Level, Risk Score
   │  └─ Outcome status (recorded/pending)
   └─ Select patient → Stage 3 (Form with History)

Stage 3: Assessment Form
   ├─ New Patient: All fields empty & editable
   └─ Returning Patient: 
       ├─ Demographic fields pre-filled & READ-ONLY
       ├─ Clinical fields empty for new data
       ├─ Hidden patient_id field set
       └─ Form shows "Follow-up Assessment" instead of "New Patient Assessment"
```

## Key Features

### 1. Patient Selection Screen
- Two prominent action cards: "New Patient" & "Returning Patient"
- Clear descriptions of each path
- Accessible via large buttons
- Back navigation to reselect if needed

### 2. Patient Search (AJAX)
- **Trigger:** Minimum 2 characters typed
- **Search Fields:** First name, last name, MRN
- **Results Display:**
  - Patient name
  - MRN
  - Age (calculated from DOB)
  - Last assessment date
  - Select button per result
- **Loading Indicator:** While searching
- **No Results:** Clear message if no matches found

### 3. Patient History Display
- **Header Panel:**
  - Patient name (large)
  - Demographics: DOB, age, gender
  - "Change Patient" button to go back to search
  
- **Assessment List:**
  - Chronological order (newest first)
  - Assessment numbering (#1, #2, #3...)
  - Color-coded by risk level:
    - 🔵 Blue border: Low risk
    - 🟠 Orange border: Moderate risk  
    - 🔴 Red border: High risk
  - Displays for each:
    - Date
    - Risk level badge
    - Risk score /100
    - Outcome status (✓ Recorded | ⏳ Pending)
  - Note: "Review above history before creating new assessment..."

### 4. Form States
**New Patient Mode:**
- All fields editable
- No history panel visible
- Form title: "New Patient Assessment"
- Submit button: "Submit Assessment"

**Returning Patient Mode:**
- Demographic fields (first_name, last_name, date_of_birth, gender) disabled
- Clinical assessment fields editable
- History panel visible above form
- Form title: "Follow-up Assessment"
- Submit button: "Submit Assessment"
- Assessment counted (e.g., "Assessment #2")

### 5. Data Flow
```
New Patient:
  Select "New Patient"
    ↓
  Fill demographic & clinical data
    ↓
  Submit to /submit_assessment.php
    ↓
  patient_id is NULL → Create patient via Patient::createPatient()
    ↓
  Create assessment linked to new patient_id
    ↓
  Calculate risk via RiskScoringEngine
    ↓
  Compare against empty history (baseline)

Returning Patient:
  Select "Returning Patient"
    ↓
  Search & select from existing patients
    ↓
  patient_id = 123 (fetched from search)
    ↓
  Demographics auto-filled (read-only)
    ↓
  Fill NEW clinical data only
    ↓
  Submit to /submit_assessment.php
    ↓
  patient_id = 123 (not null) → Skip patient creation
    ↓
  Create assessment linked to SAME patient_id
    ↓
  Calculate risk via RiskScoringEngine
    ↓
  Fetch previous assessments: getPatientAssessments(patient_id)
    ↓
  Compare against history via RiskComparisonEngine
    ↓
  Adjust risk ±30 points based on trends
```

## Files Modified/Created

### Views
- **`views/patient_assessment.php`** (Completely rewritten)
  - Added two-stage HTML structure (selection → search → form)
  - Integrated AJAX patient search
  - Added patient history display
  - Implemented form field toggling
  - Added client-side age calculation
  - HTML escaping for security
  - Dynamic styling for risk levels

### Controllers
- **`controllers/PatientController.php`** (Enhanced)
  - Added `searchPatientAPI()` method
    - Handles POST requests with search_term
    - Returns JSON with matching patients + last assessment date
    - Minimum 2-character search requirement

- **`controllers/AssessmentController.php`** (Enhanced)
  - Added `getPatientAssessmentsAPI()` method
    - Handles POST requests with patient_id
    - Returns JSON with all assessments for patient
    - Includes outcome status (recorded/pending)

### Models
- **`models/Assessment.php`** (Enhanced)
  - Added `getLastPatientAssessment($patient_id)` method
    - Returns most recent assessment with full details
    - Used to show "Last assessment: [date]" in search results

### Routes
- **`index.php`** (Enhanced)
  - Added `api-patient-search` route → PatientController::searchPatientAPI()
  - Added `api-patient-assessments` route → AssessmentController::getPatientAssessmentsAPI()
  - Both routes check for doctor/admin authorization

### Backend Handling
- **`submit_assessment.php`** (No changes needed)
  - Already checks: `$patient_id = isset($_POST['patient_id']) ? intval($_POST['patient_id']) : null;`
  - If patient_id is present: Uses it (returning patient)
  - If patient_id is null: Creates new patient (new patient)
  - RiskComparisonEngine already receives previousAssessments array

## JavaScript Functions (client-side)

### Selection Flow
- `selectNewPatient()` - Show form, hide search/history
- `selectReturningPatient()` - Show search, hide form/history
- `backToSelection()` - Return to patient selection screen
- `backToPatientSearch()` - Return to search from form

### Search & Patient Selection
- Search input event listener (triggers on input)
- `displaySearchResults(patients)` - Render search results
- `selectPatient(id, firstName, lastName, dob, gender)` - Load patient & history
- `calculateAge(dateOfBirth)` - Calculate current age
- `htmlEscape(text)` - Security: escape HTML entities

### History Display
- `displayAssessmentHistory(assessments)` - Render previous assessments
  - Assessment numbering (newest first)
  - Risk-level color coding
  - Outcome status checking

### Form Toggle
- Weight loss details (checkbox toggle)
- Smoking years (conditional on smoking status)
- Family cancer details (checkbox toggle)

## Database Impact

### No Schema Changes Required
- Existing tables support this workflow:
  - `patients`: Stores one record per patient
  - `assessments`: Links to patient_id, stores multiple assessments per patient
  - `risk_results`: Links to assessment_id for each result
  - `patient_outcomes`: Links to assessment_id for outcome tracking

### Data Integrity
- By using existing patient_id: Prevents duplicates
- Foreign key: assessment.patient_id → patients.id
- All assessments for same patient linked to same patient record
- RiskComparisonEngine can find all historical assessments

## Testing Scenarios

### Scenario 1: New Patient First Assessment
1. Click "New Patient"
2. Enter: John Doe, DOB: 1975-05-20, Male
3. Fill clinical form
4. Submit
5. **Expected:** Create patient, assessment, calculate baseline risk (no comparison)

### Scenario 2: Returning Patient Second Assessment
1. Click "Returning Patient"
2. Search: "John" 
3. Select "John Doe" from results
4. **Expected:** Demographics auto-filled (read-only)
   - Previous assessment #1 shown with risk/date
   - Form displays "Follow-up Assessment"
5. Fill new clinical data (only assessment-specific fields)
6. Submit
7. **Expected:** 
   - No new patient created
   - Assessment attached to same patient_id
   - RiskComparisonEngine compares against assessment #1
   - Risk score adjusted ±30 points based on trend

### Scenario 3: Returning Patient - Change Patient
1. Click "Returning Patient"
2. Search and select "Jane Doe"
3. Click "Change Patient"
4. **Expected:** Return to search, clear selection, search again
5. Search and select "John Doe"
6. **Expected:** Form shows John's data, history, demographics

### Scenario 4: Patient with Multiple Assessments
1. Return patient with 3 previous assessments
2. Search and select
3. **Expected:** 
   - History shows Assessment #1, #2, #3 (newest first)
   - Risk levels color-coded
   - Outcome statuses shown
   - Note about comparing new findings against history

## Integration with Phase 3 & 4A

### RiskScoringEngine (Phase 3)
- Still called: `RiskScoringEngine::calculateRisk($assessmentData)`
- Now has proper historical context via patient deduplication

### RiskComparisonEngine (Phase 4A)
- Can now properly fetch previous assessments: `getPatientAssessments($patient_id)`
- All assessments for same patient linked correctly
- Trend analysis now works (improving/stable/worsening)
- Adjustment factor (-30 to +30) properly applied

### PatientOutcome Tracking (Phase 4A)
- Still records outcomes per assessment
- Displays outcome status in history (recorded/pending)
- Population learning works with correct patient grouping

## Security Considerations

1. **Authorization:** Both API endpoints check doctor/admin role
2. **Input Validation:** 
   - Search term minimum 2 characters
   - patient_id sanitized via intval()
   - POST method validation
3. **XSS Prevention:**
   - htmlspecialchars() in Patient model search
   - htmlEscape() in JavaScript for display
   - No direct DOM innerHTML without escaping
4. **SQL Injection:** PDO prepared statements in all queries

## Performance Considerations

- Search requires 2+ characters (prevents massive result sets)
- Limit 50 patients returned per search
- Assessment history displays in reverse order (newest first)
- Single DB query per patient for assessment history
- Outcome status checked via separate indexed query

## Future Enhancements (Post-Phase 4B)

1. **Assessment Templates**
   - Load previous assessment data as template
   - "Use previous values" quick-fill option
   
2. **Bulk Assessments**
   - Queue multiple assessments per patient
   - Batch submit with comparison
   
3. **Patient Comparison**
   - Show assessment side-by-side comparison
   - Visual trend graphs (risk score over time)
   
4. **Smart Search**
   - Search by patient ID
   - Search by diagnosis or symptoms
   - Filter by date range
   
5. **Mobile Optimization**
   - Touch-friendly selection buttons
   - Simplified search interface
   
6. **Assessment Versioning**
   - Allow editing previous assessments
   - Track modification history
   - Show "last modified" timestamp

## Deployment Checklist

✅ Views updated with patient selection UI  
✅ Controllers enhanced with API methods  
✅ Models enhanced with helper methods  
✅ Routes added to index.php  
✅ JavaScript functions implemented  
✅ Security checks in place  
✅ Database compatibility verified  
✅ Phase 3 & 4A integration maintained  
✅ Git commit completed  
✅ GitHub pushed (commit: cc6b3d3)  

## Summary

Phase 4B successfully implements the patient selection workflow that:
- ✅ Prevents duplicate patient records
- ✅ Enables incremental data attachment
- ✅ Allows review of patient history before assessment
- ✅ Maintains backward compatibility with Phase 3 & 4A
- ✅ Integrates with RiskComparisonEngine
- ✅ Supports both new and returning patient workflows
- ✅ Provides AJAX patient search
- ✅ Displays assessment history with risk context

The system now properly supports the complete clinical workflow:
1. Doctor selects patient type
2. Doctor reviews patient history (if returning)
3. Doctor fills assessment form
4. System automatically compares against history
5. Risk is adjusted based on trends
6. Population learning tracks outcomes

**Next Phase:** Phase 4C can now focus on additional features like assessment templates, visual trends, or bulk operations, knowing that the foundational patient workflow is solid.

---
**Files Changed:** 5  
**Lines Added:** 725  
**Lines Removed:** 31  
**Net Change:** +694 lines  
**Commit:** Phase 4B: Implement patient selection UI for new vs returning patients
