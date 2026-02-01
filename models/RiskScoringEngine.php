<?php
/**
 * RISK SCORING ENGINE
 * ============================================================================
 * Implements evidence-based risk calculation for throat cancer assessment
 * 
 * Risk Factors (based on medical literature for head & neck cancers):
 * - Smoking: Major risk factor (dose-dependent)
 * - Alcohol: Major risk factor (synergistic with smoking)
 * - HPV: Significant risk for oropharyngeal cancer
 * - Symptoms: Persistent symptoms increase suspicion
 * - Lab values: Anemia indicator
 * - Family history: Increases baseline risk
 * ============================================================================
 */

class RiskScoringEngine {
    
    /**
     * Calculate comprehensive risk score for throat cancer
     * 
     * @param array $assessmentData - Assessment form data
     * @return array - [risk_score (0-100), risk_level, confidence, primary_factors, secondary_factors, recommendation]
     */
    public static function calculateRisk($assessmentData) {
        // Initialize scores
        $smoking_score = 0;
        $alcohol_score = 0;
        $hpv_score = 0;
        $symptoms_score = 0;
        $lab_score = 0;
        $family_history_score = 0;
        $factors = [];
        
        // ===== SMOKING RISK (0-30 points max) =====
        $smoking_status = $assessmentData['smoking_status'] ?? 'never';
        $smoking_years = intval($assessmentData['smoking_years'] ?? 0);
        
        if ($smoking_status === 'never') {
            $smoking_score = 0;
        } elseif ($smoking_status === 'former') {
            // Former smokers have reduced but still elevated risk
            if ($smoking_years >= 20) {
                $smoking_score = 15; // 15-20 years smoking = moderate risk
            } elseif ($smoking_years >= 10) {
                $smoking_score = 12;
            } else {
                $smoking_score = 8;
            }
        } elseif ($smoking_status === 'current') {
            // Current smokers have highest smoking-related risk
            if ($smoking_years >= 30) {
                $smoking_score = 30; // 30+ years = severe risk
            } elseif ($smoking_years >= 20) {
                $smoking_score = 25; // 20-30 years = high risk
            } elseif ($smoking_years >= 10) {
                $smoking_score = 20; // 10-20 years = moderate-high
            } else {
                $smoking_score = 15; // <10 years = moderate risk
            }
        }
        
        if ($smoking_score > 0) {
            $factors[] = "Smoking: {$smoking_status} smoker" . ($smoking_years > 0 ? " ({$smoking_years} years)" : "");
        }
        
        // ===== ALCOHOL RISK (0-20 points max) =====
        $alcohol_consumption = $assessmentData['alcohol_consumption'] ?? 'none';
        
        if ($alcohol_consumption === 'none') {
            $alcohol_score = 0;
        } elseif ($alcohol_consumption === 'mild') {
            $alcohol_score = 5;
        } elseif ($alcohol_consumption === 'moderate') {
            $alcohol_score = 12;
        } elseif ($alcohol_consumption === 'heavy') {
            $alcohol_score = 20;
        }
        
        // Synergistic effect: smoking + alcohol increases risk multiplicatively
        if ($smoking_score > 10 && $alcohol_score > 0) {
            $alcohol_score = min(20, $alcohol_score * 1.3); // 30% increase for combined risk
            $factors[] = "Smoking-Alcohol Synergy";
        }
        
        if ($alcohol_score > 0) {
            $factors[] = "Alcohol: {$alcohol_consumption} consumption";
        }
        
        // ===== HPV STATUS RISK (0-15 points) =====
        $hpv_status = $assessmentData['hpv_status'] ?? 'unknown';
        
        if ($hpv_status === 'positive') {
            $hpv_score = 15; // Significant risk for oropharyngeal cancer
            $factors[] = "HPV Positive (High risk)";
        } elseif ($hpv_status === 'unknown') {
            $hpv_score = 8; // Uncertain - moderate risk assumption
        } else {
            $hpv_score = 0;
        }
        
        // ===== SYMPTOMS SCORE (0-20 points) =====
        // Persistent symptoms are primary indicator for cancer workup
        
        // Sore throat duration
        $sore_throat_duration = intval($assessmentData['sore_throat_duration'] ?? 0);
        if ($sore_throat_duration > 4) {
            $symptoms_score += 8; // Persistent >4 weeks is concerning
            $factors[] = "Persistent sore throat ({$sore_throat_duration} weeks)";
        } elseif ($sore_throat_duration > 0) {
            $symptoms_score += 3;
        }
        
        // Voice changes / hoarseness
        $voice_changes = intval($assessmentData['voice_changes'] ?? 0);
        if ($voice_changes) {
            $symptoms_score += 6;
            $factors[] = "Voice changes/Hoarseness";
        }
        
        // Difficulty swallowing
        $difficulty_swallowing = intval($assessmentData['difficulty_swallowing'] ?? 0);
        if ($difficulty_swallowing) {
            $symptoms_score += 7;
            $factors[] = "Difficulty swallowing";
        }
        
        // Neck lump
        $neck_lump = intval($assessmentData['neck_lump'] ?? 0);
        if ($neck_lump) {
            $symptoms_score += 10; // Palpable mass is very concerning
            $factors[] = "Neck lump/Mass";
        }
        
        // Weight loss
        $weight_loss = intval($assessmentData['weight_loss'] ?? 0);
        $weight_loss_percentage = floatval($assessmentData['weight_loss_percentage'] ?? 0);
        if ($weight_loss && $weight_loss_percentage > 5) {
            $symptoms_score += 8;
            $factors[] = "Unexplained weight loss ({$weight_loss_percentage}%)";
        } elseif ($weight_loss) {
            $symptoms_score += 4;
        }
        
        // Cap symptoms at max
        $symptoms_score = min(20, $symptoms_score);
        
        // ===== LABORATORY FINDINGS (0-10 points) =====
        // Anemia may indicate chronic disease or malignancy
        $hemoglobin = floatval($assessmentData['hemoglobin_level'] ?? 0);
        if ($hemoglobin > 0 && $hemoglobin < 12) {
            // Moderate anemia (normal >12 for women, >13.5 for men)
            $lab_score = 8;
            $factors[] = "Anemia (Hgb: {$hemoglobin})";
        } elseif ($hemoglobin > 0 && $hemoglobin < 10) {
            // Severe anemia
            $lab_score = 10;
            $factors[] = "Severe anemia (Hgb: {$hemoglobin})";
        }
        
        // ===== FAMILY HISTORY (0-5 points) =====
        $family_cancer = intval($assessmentData['family_cancer_history'] ?? 0);
        if ($family_cancer) {
            $family_history_score = 5;
            $family_cancer_type = $assessmentData['family_cancer_type'] ?? 'Cancer';
            $factors[] = "Family history: {$family_cancer_type}";
        }
        
        // ===== CALCULATE FINAL RISK SCORE =====
        $risk_score = $smoking_score + $alcohol_score + $hpv_score + 
                      $symptoms_score + $lab_score + $family_history_score;
        
        // Cap at 100
        $risk_score = min(100, max(0, $risk_score));
        
        // ===== DETERMINE RISK LEVEL =====
        if ($risk_score >= 65) {
            $risk_level = 'High';
            $confidence = 85 + ($risk_score - 65) / 35 * 10; // 85-95%
        } elseif ($risk_score >= 40) {
            $risk_level = 'Moderate';
            $confidence = 75 + ($risk_score - 40) / 25 * 10; // 75-85%
        } else {
            $risk_level = 'Low';
            $confidence = 60 + ($risk_score / 40) * 20; // 60-80%
        }
        
        $confidence = min(95, max(60, round($confidence, 1)));
        
        // ===== GENERATE RECOMMENDATIONS =====
        $recommendations = self::generateRecommendations($risk_score, $risk_level, $factors);
        
        // ===== PREPARE FACTORS STRINGS =====
        $primary_factors = implode('; ', array_slice($factors, 0, 3)); // Top 3 factors
        $secondary_factors = implode('; ', array_slice($factors, 3)); // Remaining factors
        
        if (empty($primary_factors)) {
            $primary_factors = "Standard risk profile";
        }
        if (empty($secondary_factors)) {
            $secondary_factors = "No additional risk factors identified";
        }
        
        return [
            'risk_score' => round($risk_score, 1),
            'risk_level' => $risk_level,
            'confidence' => $confidence,
            'primary_factors' => $primary_factors,
            'secondary_factors' => $secondary_factors,
            'recommendation' => $recommendations,
            'breakdown' => [
                'smoking' => $smoking_score,
                'alcohol' => $alcohol_score,
                'hpv' => $hpv_score,
                'symptoms' => $symptoms_score,
                'lab' => $lab_score,
                'family_history' => $family_history_score
            ]
        ];
    }
    
    /**
     * Generate clinical recommendations based on risk level
     */
    private static function generateRecommendations($risk_score, $risk_level, $factors) {
        $recommendations = [];
        
        if ($risk_level === 'High') {
            $recommendations[] = "⚠️ HIGH RISK: Recommend urgent specialist referral (ENT/Oncology)";
            $recommendations[] = "Advanced imaging: CT/MRI of head and neck region";
            $recommendations[] = "Endoscopic examination recommended";
            
            if (in_array('Neck lump/Mass', $factors)) {
                $recommendations[] = "Biopsy of neck lesion for pathological examination";
            }
            
            $recommendations[] = "Follow-up assessment every 2-4 weeks";
            
        } elseif ($risk_level === 'Moderate') {
            $recommendations[] = "MODERATE RISK: Recommend ENT consultation";
            $recommendations[] = "Clinical examination and laryngoscopy";
            $recommendations[] = "Consider imaging if symptoms persist or worsen";
            $recommendations[] = "Follow-up assessment in 4-8 weeks";
            
            if (count($factors) >= 3) {
                $recommendations[] = "Multiple risk factors present - closer monitoring advised";
            }
            
        } else { // Low risk
            $recommendations[] = "LOW RISK: Continue routine monitoring";
            $recommendations[] = "Annual health screening recommended";
            $recommendations[] = "Counsel on lifestyle modifications (smoking/alcohol cessation)";
            $recommendations[] = "Return if new symptoms develop";
        }
        
        // General recommendations
        $recommendations[] = "Smoking cessation counseling strongly recommended";
        $recommendations[] = "Limit alcohol consumption";
        
        return implode("\n", $recommendations);
    }
    
    /**
     * Get risk score explanation for patient education
     */
    public static function getRiskScoreExplanation($risk_score, $risk_level) {
        $explanation = "Risk Score: {$risk_score}/100 ({$risk_level})\n\n";
        
        if ($risk_level === 'High') {
            $explanation .= "This score indicates significant risk factors for throat cancer. " .
                           "Professional evaluation by an ENT specialist is strongly recommended. " .
                           "Early detection and intervention can improve outcomes significantly.";
        } elseif ($risk_level === 'Moderate') {
            $explanation .= "This score indicates moderate risk factors present. " .
                           "Professional evaluation is recommended to rule out malignancy. " .
                           "Regular monitoring and lifestyle modifications are important.";
        } else {
            $explanation .= "This score indicates lower risk based on current evaluation. " .
                           "However, any persistent symptoms warrant professional evaluation. " .
                           "Regular health monitoring is still recommended.";
        }
        
        return $explanation;
    }
}
?>
