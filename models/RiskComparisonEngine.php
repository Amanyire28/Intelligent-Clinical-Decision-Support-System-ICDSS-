<?php
/**
 * RISK COMPARISON ENGINE
 * ============================================================================
 * Analyzes patient historical data to enhance current risk assessment
 * Implements Pattern-based learning from previous assessments
 * 
 * Features:
 * - Trend analysis (improving/stable/worsening)
 * - Symptom progression tracking
 * - Behavioral change detection
 * - Lab value trend analysis
 * - Population cohort insights
 * ============================================================================
 */

class RiskComparisonEngine {
    private $db;
    
    public function __construct($database = null) {
        $this->db = $database;
    }
    
    /**
     * Analyze current assessment with historical patient data
     * 
     * @param array $currentAssessment - Current assessment data
     * @param float $currentRiskScore - Base risk score from RiskScoringEngine
     * @param array $previousAssessments - Array of previous assessments (ordered DESC by date)
     * @return array - Enhanced analysis with trend data
     */
    public static function analyzeWithHistory($currentAssessment, $currentRiskScore, $previousAssessments = []) {
        // If no history, return current score as-is
        if (empty($previousAssessments)) {
            return [
                'adjusted_risk_score' => $currentRiskScore,
                'adjusted_risk_level' => self::getRiskLevel($currentRiskScore),
                'confidence' => 75, // Base confidence without history
                'trend_direction' => 'baseline', // First assessment
                'trend_severity' => 'none',
                'comparison_data' => [],
                'has_history' => false,
                'insights' => 'First assessment - establishing baseline.'
            ];
        }
        
        // Get most recent previous assessment for direct comparison
        $mostRecent = $previousAssessments[0];
        
        // ===== TREND ANALYSIS =====
        $previousScore = floatval($mostRecent['risk_score'] ?? 0);
        $scoreDifference = $currentRiskScore - $previousScore;
        $scoreChangePercent = $previousScore > 0 ? ($scoreDifference / $previousScore * 100) : 0;
        
        // Determine trend direction
        if ($scoreDifference > 15) {
            $trendDirection = 'worsening';
            $trendSeverity = 'rapid'; // >15 point jump
        } elseif ($scoreDifference > 5) {
            $trendDirection = 'worsening';
            $trendSeverity = 'gradual';
        } elseif ($scoreDifference < -5) {
            $trendDirection = 'improving';
            $trendSeverity = 'improving';
        } else {
            $trendDirection = 'stable';
            $trendSeverity = 'none';
        }
        
        // ===== SYMPTOM PROGRESSION ANALYSIS =====
        $symptomProgression = self::analyzeSymptomProgression($currentAssessment, $mostRecent);
        
        // ===== BEHAVIORAL CHANGE ANALYSIS =====
        $behavioralChanges = self::analyzeBehavioralChanges($currentAssessment, $mostRecent);
        
        // ===== LAB VALUE TREND ANALYSIS =====
        $labTrends = self::analyzeLabTrends($currentAssessment, $previousAssessments);
        
        // ===== TEMPORAL PATTERN ANALYSIS =====
        $temporalPattern = self::analyzeTemporalPattern($previousAssessments);
        
        // ===== CALCULATE ADJUSTMENT FACTOR =====
        $adjustmentFactor = 0;
        
        // Score trajectory adjustment (-30 to +30 points)
        if ($trendSeverity === 'rapid') {
            $adjustmentFactor += min(20, $scoreChangePercent / 5); // Up to +20 for rapid worsening
        } elseif ($trendSeverity === 'gradual' && $trendDirection === 'worsening') {
            $adjustmentFactor += min(10, $scoreChangePercent / 10); // Up to +10 for gradual worsening
        } elseif ($trendDirection === 'improving') {
            $adjustmentFactor += min(-15, $scoreChangePercent / 6); // Down to -15 for improving
        }
        
        // Symptom progression multiplier
        if ($symptomProgression['has_progression']) {
            $adjustmentFactor += $symptomProgression['progression_score'];
        }
        
        // Behavioral relapse penalty
        if (!empty($behavioralChanges)) {
            $adjustmentFactor += $behavioralChanges['adjustment'];
        }
        
        // Lab decline concern
        if ($labTrends['has_concerning_decline']) {
            $adjustmentFactor += $labTrends['adjustment'];
        }
        
        // Cap adjustment
        $adjustmentFactor = max(-30, min(30, $adjustmentFactor));
        
        // ===== CALCULATE ADJUSTED SCORE =====
        $adjustedScore = $currentRiskScore + $adjustmentFactor;
        $adjustedScore = max(0, min(100, $adjustedScore)); // Clamp 0-100
        
        // ===== ADJUST CONFIDENCE BASED ON CONSISTENCY =====
        $confidenceAdjustment = self::calculateConfidenceAdjustment(
            $symptomProgression,
            $behavioralChanges,
            $labTrends,
            $trendDirection
        );
        
        // Base confidence from RiskScoringEngine (varies 60-95%)
        $baseConfidence = self::getBaseConfidence($currentRiskScore);
        $adjustedConfidence = min(98, max(60, $baseConfidence + $confidenceAdjustment));
        
        // ===== GENERATE ENHANCED INSIGHTS =====
        $insights = self::generateInsights(
            $currentRiskScore,
            $adjustedScore,
            $trendDirection,
            $symptomProgression,
            $behavioralChanges,
            $labTrends,
            $temporalPattern
        );
        
        return [
            'adjusted_risk_score' => round($adjustedScore, 1),
            'adjusted_risk_level' => self::getRiskLevel($adjustedScore),
            'confidence' => round($adjustedConfidence, 1),
            'trend_direction' => $trendDirection,
            'trend_severity' => $trendSeverity,
            'base_score' => round($currentRiskScore, 1),
            'adjustment_factor' => round($adjustmentFactor, 1),
            'score_change_from_previous' => round($scoreDifference, 1),
            'score_change_percent' => round($scoreChangePercent, 1),
            'has_history' => true,
            'comparison_data' => [
                'previous_score' => round($previousScore, 1),
                'previous_risk_level' => $mostRecent['risk_level'] ?? 'Low',
                'previous_assessment_date' => $mostRecent['assessment_date'] ?? 'Unknown',
                'days_since_previous' => self::daysSincePreviousAssessment($mostRecent['assessment_date'] ?? null),
                'symptom_progression' => $symptomProgression,
                'behavioral_changes' => $behavioralChanges,
                'lab_trends' => $labTrends,
                'temporal_pattern' => $temporalPattern
            ],
            'insights' => $insights
        ];
    }
    
    /**
     * Analyze symptom progression between assessments
     */
    private static function analyzeSymptomProgression($current, $previous) {
        $progression = [
            'has_progression' => false,
            'progression_score' => 0,
            'worsened_symptoms' => [],
            'new_symptoms' => [],
            'improved_symptoms' => []
        ];
        
        // Sore throat duration
        $currentThroat = intval($current['sore_throat_duration'] ?? 0);
        $previousThroat = intval($previous['sore_throat_duration'] ?? 0);
        if ($currentThroat > $previousThroat + 2) {
            $progression['worsened_symptoms'][] = "Sore throat duration increased: {$previousThroat}w → {$currentThroat}w";
            $progression['progression_score'] += 5;
            $progression['has_progression'] = true;
        } elseif ($currentThroat < $previousThroat - 1) {
            $progression['improved_symptoms'][] = "Sore throat duration decreased";
            $progression['progression_score'] -= 3;
        }
        
        // Voice changes
        $currentVoice = intval($current['voice_changes'] ?? 0);
        $previousVoice = intval($previous['voice_changes'] ?? 0);
        if ($currentVoice && !$previousVoice) {
            $progression['new_symptoms'][] = "Voice changes/Hoarseness (NEW)";
            $progression['progression_score'] += 6;
            $progression['has_progression'] = true;
        }
        
        // Difficulty swallowing
        $currentDysph = intval($current['difficulty_swallowing'] ?? 0);
        $previousDysph = intval($previous['difficulty_swallowing'] ?? 0);
        if ($currentDysph && !$previousDysph) {
            $progression['new_symptoms'][] = "Difficulty swallowing (NEW)";
            $progression['progression_score'] += 7;
            $progression['has_progression'] = true;
        }
        
        // Neck lump
        $currentLump = intval($current['neck_lump'] ?? 0);
        $previousLump = intval($previous['neck_lump'] ?? 0);
        if ($currentLump && !$previousLump) {
            $progression['new_symptoms'][] = "Neck lump/Mass (NEW - HIGH CONCERN)";
            $progression['progression_score'] += 10;
            $progression['has_progression'] = true;
        }
        
        // Weight loss
        $currentWL = floatval($current['weight_loss_percentage'] ?? 0);
        $previousWL = floatval($previous['weight_loss_percentage'] ?? 0);
        if ($currentWL > $previousWL + 2) {
            $progression['worsened_symptoms'][] = "Weight loss increased: {$previousWL}% → {$currentWL}%";
            $progression['progression_score'] += 5;
            $progression['has_progression'] = true;
        }
        
        // Cap progression score
        $progression['progression_score'] = min(20, $progression['progression_score']);
        
        return $progression;
    }
    
    /**
     * Analyze behavioral changes (smoking, alcohol)
     */
    private static function analyzeBehavioralChanges($current, $previous) {
        $changes = [
            'adjustment' => 0,
            'smoking_change' => null,
            'alcohol_change' => null,
            'is_relapse' => false
        ];
        
        $currentSmoke = $current['smoking_status'] ?? 'never';
        $previousSmoke = $previous['smoking_status'] ?? 'never';
        
        // Smoking relapse (former/never → current)
        if (($previousSmoke === 'never' || $previousSmoke === 'former') && $currentSmoke === 'current') {
            $changes['smoking_change'] = "Smoking RELAPSE: {$previousSmoke} → current ({$current['smoking_years']} years)";
            $changes['adjustment'] += 8; // Major concern
            $changes['is_relapse'] = true;
        }
        // Smoking escalation
        elseif ($previousSmoke === 'never' && $currentSmoke === 'former') {
            $changes['smoking_change'] = "Smoking status changed: never → former";
            $changes['adjustment'] += 2;
        }
        
        // Alcohol increase
        $currentAlc = $current['alcohol_consumption'] ?? 'none';
        $previousAlc = $previous['alcohol_consumption'] ?? 'none';
        $alcScale = ['none' => 0, 'mild' => 1, 'moderate' => 2, 'heavy' => 3];
        
        if (($alcScale[$currentAlc] ?? 0) > ($alcScale[$previousAlc] ?? 0)) {
            $changes['alcohol_change'] = "Alcohol consumption increased: {$previousAlc} → {$currentAlc}";
            $changes['adjustment'] += 3;
        }
        
        return $changes;
    }
    
    /**
     * Analyze lab value trends across multiple assessments
     */
    private static function analyzeLabTrends($current, $allPrevious) {
        $trends = [
            'has_concerning_decline' => false,
            'adjustment' => 0,
            'hemoglobin_trend' => null,
            'lymphocyte_trend' => null
        ];
        
        $currentHgb = floatval($current['hemoglobin_level'] ?? 0);
        
        if ($currentHgb > 0) {
            // Check against most recent previous
            $previousHgb = floatval($allPrevious[0]['hemoglobin_level'] ?? 0);
            
            if ($previousHgb > 0) {
                $hgbDecline = $previousHgb - $currentHgb;
                
                // Significant decline
                if ($hgbDecline > 1.0) {
                    $trends['hemoglobin_trend'] = "Hemoglobin significantly declining: {$previousHgb} → {$currentHgb} g/dL";
                    $trends['adjustment'] += 5;
                    $trends['has_concerning_decline'] = true;
                } elseif ($hgbDecline > 0.5) {
                    $trends['hemoglobin_trend'] = "Hemoglobin gradually declining: {$previousHgb} → {$currentHgb} g/dL";
                    $trends['adjustment'] += 2;
                }
                
                // Check if approaching anemia
                if ($currentHgb < 12 && $previousHgb >= 12) {
                    $trends['hemoglobin_trend'] = "Hemoglobin crossed into anemia range: {$previousHgb} → {$currentHgb} g/dL";
                    $trends['adjustment'] += 3;
                    $trends['has_concerning_decline'] = true;
                }
            }
        }
        
        return $trends;
    }
    
    /**
     * Analyze temporal patterns (assessment frequency)
     */
    private static function analyzeTemporalPattern($previousAssessments) {
        $pattern = [
            'assessment_frequency' => null,
            'frequency_indicator' => 'routine',
            'clinical_significance' => ''
        ];
        
        if (count($previousAssessments) >= 2) {
            $latest = $previousAssessments[0];
            $previous = $previousAssessments[1];
            
            $latestDate = strtotime($latest['assessment_date'] ?? 'now');
            $previousDate = strtotime($previous['assessment_date'] ?? 'now');
            $daysBetween = intval(($latestDate - $previousDate) / 86400);
            
            $pattern['assessment_frequency'] = "Every {$daysBetween} days";
            
            if ($daysBetween <= 7) {
                $pattern['frequency_indicator'] = 'urgent_follow_up';
                $pattern['clinical_significance'] = 'Frequent reassessments indicate clinical concern or monitoring protocol';
            } elseif ($daysBetween <= 14) {
                $pattern['frequency_indicator'] = 'close_follow_up';
                $pattern['clinical_significance'] = 'Regular follow-up monitoring ongoing';
            } else {
                $pattern['frequency_indicator'] = 'routine';
                $pattern['clinical_significance'] = 'Routine assessment interval';
            }
        }
        
        return $pattern;
    }
    
    /**
     * Calculate confidence adjustment based on data consistency
     */
    private static function calculateConfidenceAdjustment($symptomProgression, $behavioralChanges, $labTrends, $trendDirection) {
        $adjustment = 0;
        
        // Consistent worsening data increases confidence
        if ($trendDirection === 'worsening') {
            if ($symptomProgression['has_progression']) {
                $adjustment += 3;
            }
            if ($labTrends['has_concerning_decline']) {
                $adjustment += 2;
            }
        }
        
        // Behavioral relapse confirms high-risk behavior
        if (!empty($behavioralChanges['is_relapse'])) {
            $adjustment += 2;
        }
        
        return $adjustment;
    }
    
    /**
     * Get base confidence for a risk score
     */
    private static function getBaseConfidence($riskScore) {
        if ($riskScore >= 65) {
            return 85 + ($riskScore - 65) / 35 * 10; // 85-95%
        } elseif ($riskScore >= 40) {
            return 75 + ($riskScore - 40) / 25 * 10; // 75-85%
        } else {
            return 60 + ($riskScore / 40) * 20; // 60-80%
        }
    }
    
    /**
     * Determine risk level from score
     */
    private static function getRiskLevel($score) {
        if ($score >= 65) {
            return 'High';
        } elseif ($score >= 40) {
            return 'Moderate';
        } else {
            return 'Low';
        }
    }
    
    /**
     * Calculate days since previous assessment
     */
    private static function daysSincePreviousAssessment($previousDate) {
        if (!$previousDate) return null;
        $previousTime = strtotime($previousDate);
        $now = time();
        return intval(($now - $previousTime) / 86400);
    }
    
    /**
     * Generate comprehensive text insights
     */
    private static function generateInsights($baseScore, $adjustedScore, $trendDirection, $symptomProgression, $behavioralChanges, $labTrends, $temporalPattern) {
        $insights = [];
        
        // Trend summary
        if ($trendDirection === 'worsening') {
            $difference = $adjustedScore - $baseScore;
            if ($difference > 15) {
                $insights[] = "⚠️ CRITICALLY WORSENING: Risk has escalated significantly. Patient shows multiple concerning developments requiring urgent intervention.";
            } elseif ($difference > 5) {
                $insights[] = "⬆️ WORSENING TREND: Patient condition shows progressive deterioration. Multiple risk factors increasing.";
            }
        } elseif ($trendDirection === 'improving') {
            $insights[] = "✅ IMPROVING TREND: Patient condition shows positive trajectory. Continue monitoring and current management.";
        } else {
            $insights[] = "➡️ STABLE: Risk assessment remains consistent with previous evaluation.";
        }
        
        // Symptom progression details
        if (!empty($symptomProgression['new_symptoms'])) {
            $insights[] = "🔴 NEW SYMPTOMS DETECTED: " . implode(", ", $symptomProgression['new_symptoms']);
        }
        if (!empty($symptomProgression['worsened_symptoms'])) {
            $insights[] = "📈 WORSENING SYMPTOMS: " . implode(", ", $symptomProgression['worsened_symptoms']);
        }
        
        // Behavioral concerns
        if (!empty($behavioralChanges['smoking_change'])) {
            $insights[] = "⚠️ SMOKING: " . $behavioralChanges['smoking_change'];
        }
        if (!empty($behavioralChanges['alcohol_change'])) {
            $insights[] = "⚠️ ALCOHOL: " . $behavioralChanges['alcohol_change'];
        }
        
        // Lab concerns
        if (!empty($labTrends['hemoglobin_trend'])) {
            $insights[] = "🩸 LAB VALUES: " . $labTrends['hemoglobin_trend'];
        }
        
        // Temporal pattern significance
        if ($temporalPattern['frequency_indicator'] === 'urgent_follow_up') {
            $insights[] = "⏰ PATTERN: Frequent reassessments indicate clinical team has recognized concerning findings.";
        }
        
        // Recommendation summary
        $insights[] = "\n📋 CLINICAL SUMMARY: Base score " . round($baseScore, 1) . 
                     " adjusted to " . round($adjustedScore, 1) . 
                     " based on historical context. " .
                     "Recommend proceeding with urgency level indicated by adjusted risk level.";
        
        return implode("\n", $insights);
    }
}
?>
