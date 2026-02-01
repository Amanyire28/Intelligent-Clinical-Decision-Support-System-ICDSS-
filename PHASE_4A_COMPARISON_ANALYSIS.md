# PATIENT HISTORY COMPARISON ANALYSIS
## Context and Implementation Plan for Enhanced Risk Assessment

---

## CURRENT SYSTEM CONTEXT

### 1. DATABASE STRUCTURE & DATA AVAILABILITY

#### Patient Table (`patients`)
```
- id (PK)
- first_name, last_name
- date_of_birth
- gender
- contact_phone, contact_email
- medical_record_number
- created_at, updated_at
```
✅ **Current Status**: One patient record per person (no duplicates)

#### Assessments Table (`assessments`)
```
- id (PK)
- patient_id (FK to patients)
- doctor_id (FK to users)
- assessment_date
- smoking_status, smoking_years
- alcohol_consumption
- sore_throat_duration, voice_changes, difficulty_swallowing, neck_lump
- weight_loss, weight_loss_percentage, unexplained_weight_loss_weeks
- hpv_status, hpv_test_date
- family_cancer_history, family_cancer_type
- hemoglobin_level, lymphocyte_count
- clinical_notes
- created_at, updated_at
```
✅ **Current Status**: Multiple assessments per patient (full history available)

#### Risk Results Table (`risk_results`)
```
- id (PK)
- assessment_id (FK to assessments)
- risk_score (0-100)
- risk_level (Low/Moderate/High)
- confidence_percentage
- primary_factors, secondary_factors
- clinical_recommendation
- calculated_at, created_at
```
✅ **Current Status**: One risk result per assessment

### 2. EXISTING METHODS FOR HISTORICAL DATA RETRIEVAL

**Patient Model:**
- `getPatientById()` - Gets basic patient demographics

**Assessment Model:**
- `getPatientAssessments($patient_id)` - Returns ALL assessments for a patient (ordered DESC by date)
  - Returns: id, assessment_date, smoking_status, hpv_status, doctor_name, risk_level, risk_score
  - ✅ **Already provides full history access**

---

## COMPARISON LOGIC OPPORTUNITIES

### What We Can Compare:

#### 1. **TREND ANALYSIS** 
Compare current assessment with previous assessments to identify:
- ⬆️ **Worsening Risk**: Risk score increased compared to last assessment
- ⬇️ **Improving Status**: Risk score decreased (e.g., after smoking cessation)
- ↔️ **Stable Condition**: Risk score relatively unchanged
- 🔴 **Progressive Symptoms**: Symptoms worsening over time
  - Duration of sore throat increasing
  - New symptoms appearing
  - Weight loss progressing

#### 2. **TEMPORAL PATTERNS**
- How frequently is patient being assessed? (rapid follow-ups vs. long gaps)
- Assessment pattern indicates urgency level
- "First assessment" vs. "follow-up" context changes recommendations

#### 3. **SYMPTOM PROGRESSION**
- **Current Assessment**: Sore throat 3 weeks
- **Previous Assessment** (1 month ago): Sore throat 1 week
- **Conclusion**: Worsening → Increase urgency recommendation

#### 4. **BEHAVIORAL CHANGES**
- Smoking status: "former" → "current" (relapse!)
- Alcohol consumption: "mild" → "moderate"
- HPV Status: "unknown" → "positive" (newly tested)

#### 5. **LAB VALUE CHANGES**
- Hemoglobin: 12.5 g/dL → 11.2 g/dL (worsening anemia)
- Trend over multiple assessments suggests chronic condition

#### 6. **CONSISTENCY ASSESSMENT**
- Patient reported "no symptoms" 1 month ago, now reports "neck lump"
- Either new development OR previously unreported
- Changes confidence and recommendation

---

## IMPLEMENTATION APPROACH

### Phase 4A: Comparative Risk Scoring (RECOMMENDED)

Create new `RiskComparisonEngine` class that will:

```
INPUT: 
- Current Assessment Data
- Previous Assessments (Historical Array)
- Current Risk Score (from RiskScoringEngine)

OUTPUT:
- Adjusted Risk Score (with history context)
- Trend Analysis (improving/stable/worsening)
- Confidence Boost/Reduction (based on consistency)
- Enhanced Recommendation (accounts for history)
- Trend Explanation (why score changed from last time)
```

### Algorithm Logic:

```php
class RiskComparisonEngine {
    
    // 1. Calculate trend adjustment factor (-20 to +20 points)
    calculateTrendAdjustment(currentScore, previousScores)
    
    // 2. Detect worsening symptoms
    detectProgressiveSymptoms(currentAssessment, previousAssessment)
    
    // 3. Assess behavioral changes (smoking relapse, etc)
    assessBehavioralChanges(currentData, previousData)
    
    // 4. Analyze temporal patterns
    analyzeTemporalPatterns(allAssessments)
    
    // 5. Generate comparative recommendation
    generateComparativeRecommendation(currentScore, previousData, trends)
}
```

### Example Scenario:

**Patient Case: John Doe**

**Previous Assessment (30 days ago):**
- Risk Score: 35 (Low Risk)
- Symptoms: Mild sore throat (1 week)
- Smoking: Never smoked

**Current Assessment (Today):**
- Risk Score: 68 (High Risk) - WITHOUT HISTORY
- Symptoms: Persistent sore throat (6 weeks) + new neck lump
- Smoking: Now smokes 10 cigarettes/day (RELAPSE)

**Comparison Analysis:**
```
Base Score: 68
+ Worsening Trend: +8 points (risk increased 92% from previous)
+ New Symptoms: +5 points (neck lump is new finding)
+ Behavioral Relapse: +3 points (smoking resumption)
= ADJUSTED SCORE: 84 (Very High Risk)

RECOMMENDATION UPGRADE:
- Base: "ENT consultation recommended"
- With History: "URGENT ENT + Oncology referral. Rapidly progressive 
  symptoms + new behavioral risk factor warrants accelerated evaluation."
```

---

## DATA FLOW ARCHITECTURE

### Current Flow (Existing):
```
Assessment Form 
    ↓
RiskScoringEngine.calculateRisk(currentData)
    ↓
Risk Result Stored
    ↓
Assessment Results Displayed
```

### Enhanced Flow (Proposed):
```
Assessment Form 
    ↓
RiskScoringEngine.calculateRisk(currentData)
    ↓ [NEW] Get Patient History
    ↓
RiskComparisonEngine.analyzeWithHistory(
    currentAssessment,
    currentRiskScore,
    previousAssessments[]
)
    ↓
Enhanced Risk Result Stored (with comparison metadata)
    ↓
Assessment Results Displayed with Trend Visualization
```

---

## IMPLEMENTATION POINTS

### 1. **In Submit Assessment** (`submit_assessment.php`)
```php
// CURRENT (line ~75):
$riskCalculation = RiskScoringEngine::calculateRisk($assessmentData);

// ENHANCED:
$riskCalculation = RiskScoringEngine::calculateRisk($assessmentData);
$previousAssessments = $assessmentModel->getPatientAssessments($patient_id, 5); // Last 5
$enhancedRisk = RiskComparisonEngine::analyzeWithHistory(
    $assessmentData,
    $riskCalculation,
    $previousAssessments
);
$riskResultModel->createRiskResult(
    $assessment_id,
    $enhancedRisk['adjusted_risk_score'],  // Use adjusted score
    $enhancedRisk['adjusted_risk_level'],
    $enhancedRisk['confidence'],
    $enhancedRisk['primary_factors'],
    $enhancedRisk['secondary_factors'],
    $enhancedRisk['enhanced_recommendation']  // Include trend explanation
);
```

### 2. **In Assessment Results View** (`views/assessment_results.php`)
Display new section:
```html
<!-- TREND ANALYSIS SECTION (NEW) -->
<div class="panel panel-comparison">
    <h3>Comparison with Previous Assessments</h3>
    
    <!-- Score Trend Graph -->
    <div class="trend-chart">
        Chart showing risk scores over time (last 5-10 assessments)
    </div>
    
    <!-- Symptom Comparison Table -->
    <table>
        Previous Assessment | Current Assessment | Change
        Sore throat: 1 wk  | Sore throat: 6 wk  | ⬆️ WORSENING
        Neck lump: No      | Neck lump: Yes     | ⬆️ NEW
        Smoking: Never     | Smoking: Current   | ⬆️ RELAPSE
    </table>
    
    <!-- Trend Summary -->
    "This patient's condition has WORSENED significantly in the past 30 days.
    Risk increased from Low (35) to High (84) due to progressive symptoms and
    new behavioral risk factors. Urgent specialist referral recommended."
</div>
```

### 3. **Database Enhancement** (Optional)
Add column to `risk_results` table:
```sql
ALTER TABLE risk_results ADD COLUMN (
    comparison_data JSON,  -- Stores comparison metadata
    trend_direction ENUM('improving', 'stable', 'worsening'),
    previous_assessment_id INT
);
```

---

## BENEFITS OF COMPARISON LOGIC

| Benefit | Impact |
|---------|--------|
| **Contextual Recommendations** | Same score = different recommendation if trending worse |
| **Early Warning** | Rapid worsening despite moderate score triggers alert |
| **Confidence Improvement** | Multiple consistent findings = higher confidence |
| **Physician Insight** | Doctor understands WHY score changed from last time |
| **Pattern Detection** | Identifies recurring symptoms or cycles |
| **Follow-up Optimization** | Better timing for next assessment |

---

## IMPLEMENTATION PRIORITY

### Phase 4A: Core Comparison (HIGH PRIORITY)
- ✅ Trend detection (improving/stable/worsening)
- ✅ Symptom progression analysis
- ✅ Behavioral change detection
- ✅ Adjusted risk scoring with history

### Phase 4B: Visualization (MEDIUM PRIORITY)
- 📊 Trend charts (risk score over time)
- 📋 Side-by-side symptom comparison
- 📈 Graph showing progression patterns

### Phase 4C: Advanced Analytics (LOWER PRIORITY)
- 🤖 Machine learning pattern recognition
- 📊 Predictive scoring (likelihood of progression)
- 🎯 Personalized follow-up recommendations

---

## QUESTIONS FOR IMPLEMENTATION DECISION

1. **Comparison Window**: Compare against last assessment only, or last 3-6 months?
   - Recommendation: Last 2-3 assessments + any from past 6 months

2. **Adjustment Magnitude**: How much should history adjust the score?
   - Recommendation: ±0 to ±30 points maximum

3. **Display Complexity**: Simple text explanation or detailed charts?
   - Recommendation: Start with text + simple table, add charts later

4. **Database Tracking**: Store adjusted score separately or replace original?
   - Recommendation: Store both (original + adjusted) for audit trail

---

## READY TO PROCEED?

This analysis provides the **complete context and logic framework** for implementing 
patient history comparison. When you're ready, I can implement:

1. `RiskComparisonEngine.php` class
2. Integration in `submit_assessment.php`
3. Display updates in `assessment_results.php`
4. Optional: Database schema update

Would you like me to proceed with implementation?
