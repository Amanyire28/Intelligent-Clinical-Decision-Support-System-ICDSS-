<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment Results - ICDSS</title>
    <link rel="stylesheet" href="/CANCER/assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - Risk Assessment Results
                </h1>
            </div>
            <div class="navbar-user">
                <span class="user-info">
                    Welcome, <strong><?php echo htmlspecialchars($current_user['full_name'] ?? 'Doctor'); ?></strong>
                </span>
                <a href="/CANCER/logout.php" class="btn-logout">Logout</a>
            </div>
        </nav>

        <div class="main-content">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <div class="nav-section">
                    <h3>Quick Access</h3>
                    <ul class="nav-list">
                        <li><a href="/CANCER/index.php?page=doctor-dashboard" class="nav-link">Dashboard</a></li>
                        <li><a href="/CANCER/index.php?page=patient-assessment" class="nav-link">New Assessment</a></li>
                        <li><a href="/CANCER/index.php?page=patient-search" class="nav-link">Patient History</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <!-- Patient Information Card -->
                <div class="panel panel-info">
                    <div class="panel-header">
                        <h3>Patient Information</h3>
                    </div>
                    <div class="panel-body">
                        <div class="patient-info-row">
                            <div class="info-item">
                                <label>Patient Name:</label>
                                <p class="info-value"><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></p>
                            </div>
                            <div class="info-item">
                                <label>Age:</label>
                                <p class="info-value"><?php echo $assessment['age'] ?? 'N/A'; ?> years old</p>
                            </div>
                            <div class="info-item">
                                <label>Gender:</label>
                                <p class="info-value"><?php echo htmlspecialchars($assessment['gender'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="info-item">
                                <label>Assessment Date:</label>
                                <p class="info-value"><?php echo date('F d, Y H:i', strtotime($assessment['assessment_date'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Risk Assessment Result - Main Display -->
                <div class="result-grid">
                    <!-- Risk Score Card (Primary) -->
                    <div class="panel panel-primary result-panel">
                        <div class="panel-header">
                            <h3>Risk Assessment Score</h3>
                        </div>
                        <div class="panel-body">
                            <div class="risk-score-display">
                                <div class="score-visualization">
                                    <div class="score-circle">
                                        <span class="score-number"><?php echo number_format($risk_result['risk_score'] ?? 0, 1); ?></span>
                                        <span class="score-label">/ 100</span>
                                    </div>
                                </div>

                                <div class="risk-details">
                                    <div class="risk-level-card">
                                        <label>Risk Classification:</label>
                                        <div class="risk-level-badge badge-<?php echo strtolower($risk_result['risk_level'] ?? 'low'); ?> large">
                                            <?php echo htmlspecialchars($risk_result['risk_level'] ?? 'Low'); ?> Risk
                                        </div>
                                    </div>

                                    <div class="confidence-indicator">
                                        <label>Confidence Score:</label>
                                        <div class="progress-bar">
                                            <div class="progress-fill" style="width: <?php echo $risk_result['confidence_percentage'] ?? 0; ?>%">
                                                <span class="progress-text"><?php echo number_format($risk_result['confidence_percentage'] ?? 0, 1); ?>%</span>
                                            </div>
                                        </div>
                                        <p class="progress-note">
                                            <?php 
                                                $confidence = $risk_result['confidence_percentage'] ?? 0;
                                                if ($confidence >= 85) {
                                                    echo "High confidence assessment";
                                                } elseif ($confidence >= 70) {
                                                    echo "Moderate-to-high confidence assessment";
                                                } else {
                                                    echo "Consider additional clinical evaluation";
                                                }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Clinical Recommendation -->
                    <div class="panel panel-secondary">
                        <div class="panel-header">
                            <h3>Clinical Recommendation</h3>
                        </div>
                        <div class="panel-body">
                            <div class="recommendation-box">
                                <div class="recommendation-content">
                                    <?php 
                                        $recommendation = $risk_result['clinical_recommendation'] ?? 'Continue standard monitoring protocol.';
                                        echo nl2br(htmlspecialchars($recommendation));
                                    ?>
                                </div>
                            </div>

                            <!-- Risk Factor Summary -->
                            <div class="factors-section">
                                <h4>Key Risk Factors</h4>
                                <div class="factors-list">
                                    <div class="factor-group">
                                        <h5>Primary Factors (High Impact):</h5>
                                        <ul class="factor-items">
                                            <?php 
                                                $primary = explode(';', $risk_result['primary_factors'] ?? '');
                                                foreach ($primary as $factor) {
                                                    if (trim($factor)) {
                                                        echo '<li>' . htmlspecialchars(trim($factor)) . '</li>';
                                                    }
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                    <div class="factor-group">
                                        <h5>Secondary Factors (Moderate Impact):</h5>
                                        <ul class="factor-items">
                                            <?php 
                                                $secondary = explode(';', $risk_result['secondary_factors'] ?? '');
                                                foreach ($secondary as $factor) {
                                                    if (trim($factor)) {
                                                        echo '<li>' . htmlspecialchars(trim($factor)) . '</li>';
                                                    }
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PATIENT HISTORY COMPARISON ANALYSIS (NEW) -->
                <?php 
                    // Check if comparison data is available in clinical_recommendation
                    // The RiskComparisonEngine outputs insights in clinical_recommendation
                    $isComparison = strpos($risk_result['clinical_recommendation'] ?? '', 'WORSENING') !== false ||
                                   strpos($risk_result['clinical_recommendation'] ?? '', 'IMPROVING') !== false ||
                                   strpos($risk_result['clinical_recommendation'] ?? '', 'STABLE') !== false;
                ?>
                
                <?php if ($isComparison): ?>
                <div class="panel panel-comparison" style="border-left: 5px solid #ff9800;">
                    <div class="panel-header">
                        <h3>📈 Comparison with Patient History</h3>
                        <p class="panel-description">Analysis based on previous assessments</p>
                    </div>
                    <div class="panel-body">
                        <div class="comparison-insights">
                            <div style="background-color: #f5f5f5; padding: 15px; border-radius: 5px; line-height: 1.8;">
                                <?php 
                                    // Display the multi-line insights
                                    $insights = $risk_result['clinical_recommendation'];
                                    // Remove HTML tags and display as formatted text
                                    echo nl2br(htmlspecialchars($insights));
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Detailed Assessment Data -->
                <div class="panel panel-secondary">
                    <div class="panel-header">
                        <h3>Assessment Details</h3>
                    </div>
                    <div class="panel-body">
                        <div class="details-grid">
                            <!-- Symptoms Summary -->
                            <div class="detail-section">
                                <h4>Reported Symptoms</h4>
                                <ul class="detail-list">
                                    <li><strong>Persistent Sore Throat:</strong> <?php echo $assessment['sore_throat_duration'] ?? 0; ?> weeks</li>
                                    <li><strong>Voice Changes:</strong> <span class="badge <?php echo $assessment['voice_changes'] ? 'badge-warning' : 'badge-success'; ?>"><?php echo $assessment['voice_changes'] ? 'Present' : 'Absent'; ?></span></li>
                                    <li><strong>Difficulty Swallowing:</strong> <span class="badge <?php echo $assessment['difficulty_swallowing'] ? 'badge-warning' : 'badge-success'; ?>"><?php echo $assessment['difficulty_swallowing'] ? 'Present' : 'Absent'; ?></span></li>
                                    <li><strong>Neck Lump:</strong> <span class="badge <?php echo $assessment['neck_lump'] ? 'badge-warning' : 'badge-success'; ?>"><?php echo $assessment['neck_lump'] ? 'Present' : 'Absent'; ?></span></li>
                                    <li><strong>Unexplained Weight Loss:</strong> <span class="badge <?php echo $assessment['weight_loss'] ? 'badge-warning' : 'badge-success'; ?>"><?php echo $assessment['weight_loss'] ? 'Yes' : 'No'; ?></span></li>
                                </ul>
                            </div>

                            <!-- Lifestyle Factors -->
                            <div class="detail-section">
                                <h4>Lifestyle Factors</h4>
                                <ul class="detail-list">
                                    <li><strong>Smoking Status:</strong> <?php echo ucfirst($assessment['smoking_status'] ?? 'Unknown'); ?></li>
                                    <li><strong>Smoking Duration:</strong> <?php echo $assessment['smoking_years'] ?? 0; ?> years</li>
                                    <li><strong>Alcohol Consumption:</strong> <?php echo ucfirst($assessment['alcohol_consumption'] ?? 'Unknown'); ?></li>
                                </ul>
                            </div>

                            <!-- Medical History -->
                            <div class="detail-section">
                                <h4>Medical History</h4>
                                <ul class="detail-list">
                                    <li><strong>HPV Status:</strong> <?php echo ucfirst($assessment['hpv_status'] ?? 'Unknown'); ?></li>
                                    <li><strong>Family Cancer History:</strong> <span class="badge <?php echo $assessment['family_cancer_history'] ? 'badge-warning' : 'badge-success'; ?>"><?php echo $assessment['family_cancer_history'] ? 'Yes' : 'No'; ?></span></li>
                                    <li><strong>Cancer Types:</strong> <?php echo htmlspecialchars($assessment['family_cancer_type'] ?? 'N/A'); ?></li>
                                </ul>
                            </div>

                            <!-- Lab Indicators -->
                            <div class="detail-section">
                                <h4>Lab Indicators</h4>
                                <ul class="detail-list">
                                    <li><strong>Hemoglobin:</strong> <?php echo $assessment['hemoglobin_level'] ?? 'Not recorded'; ?> g/dL <span class="text-muted">(Normal: 13.5-17.5)</span></li>
                                    <li><strong>Lymphocyte Count:</strong> <?php echo $assessment['lymphocyte_count'] ?? 'Not recorded'; ?> K/uL <span class="text-muted">(Normal: 1.0-4.8)</span></li>
                                </ul>
                            </div>
                        </div>

                        <!-- Clinical Notes -->
                        <?php if (!empty($assessment['clinical_notes'])): ?>
                            <div class="clinical-notes-section">
                                <h4>Clinical Notes</h4>
                                <p class="clinical-notes-content">
                                    <?php echo nl2br(htmlspecialchars($assessment['clinical_notes'])); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Historical Decision Support Panel -->
                <div class="panel panel-info">
                    <div class="panel-header">
                        <h3>📊 Historical Decision Support</h3>
                        <p class="panel-subtitle">Similar cases and outcomes from historical data</p>
                    </div>
                    <div class="panel-body">
                        <div id="historicalInsights" style="text-align: center; padding: 30px; color: #666;">
                            <p>⏳ Loading historical insights...</p>
                        </div>
                    </div>
                </div>

                <!-- Outcome Recording Panel -->
                <div class="panel panel-primary">
                    <div class="panel-header">
                        <h3>📋 Record Clinical Outcome & Diagnosis</h3>
                        <p class="panel-subtitle">Document your findings, diagnosis, and treatment plan</p>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="/CANCER/index.php?page=api-outcome-action" id="outcomeForm">
                            <input type="hidden" name="action" value="record">
                            <input type="hidden" name="assessment_id" value="<?php echo htmlspecialchars($assessment['id']); ?>">
                            
                            <!-- Section 1: Diagnosis -->
                            <div class="form-section" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                                <h4 style="color: #333; margin-bottom: 15px;">🔍 Diagnosis</h4>
                                
                                <div class="form-group">
                                    <label for="final_diagnosis" class="form-label"><strong>Final Diagnosis:</strong> *</label>
                                    <select id="final_diagnosis" name="final_diagnosis" class="form-control" required>
                                        <option value="">-- Select diagnosis --</option>
                                        <option value="Malignant">🚨 Malignant (Cancer Confirmed)</option>
                                        <option value="Benign">✅ Benign Condition</option>
                                        <option value="Pending">⏳ Pending Further Investigation</option>
                                        <option value="Unknown">❓ Unknown/Inconclusive</option>
                                    </select>
                                </div>

                                <!-- Cancer Details (shown only if Malignant selected) -->
                                <div id="cancerDetailsSection" style="display: none; background: #fff3e0; padding: 15px; border-radius: 5px; margin-top: 15px;">
                                    <div class="form-group">
                                        <label for="cancer_type" class="form-label">Cancer Type:</label>
                                        <select id="cancer_type" name="cancer_type" class="form-control">
                                            <option value="">-- Select type --</option>
                                            <option value="Squamous Cell Carcinoma">Squamous Cell Carcinoma</option>
                                            <option value="Adenocarcinoma">Adenocarcinoma</option>
                                            <option value="Salivary Gland Cancer">Salivary Gland Cancer</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="cancer_stage" class="form-label">Cancer Stage (TNM Classification):</label>
                                        <select id="cancer_stage" name="cancer_stage" class="form-control">
                                            <option value="">-- Select stage --</option>
                                            <option value="Stage 1">Stage 1 (Early)</option>
                                            <option value="Stage 2">Stage 2 (Localized)</option>
                                            <option value="Stage 3">Stage 3 (Advanced)</option>
                                            <option value="Stage 4">Stage 4 (Metastatic)</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="tumor_location" class="form-label">Tumor Location:</label>
                                        <input type="text" id="tumor_location" name="tumor_location" class="form-control" placeholder="e.g., Larynx, Pharynx, Tongue">
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Treatment Plan -->
                            <div class="form-section" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                                <h4 style="color: #333; margin-bottom: 15px;">💊 Treatment Plan</h4>
                                
                                <div class="form-group">
                                    <label class="form-label"><strong>Recommended Treatment:</strong> *</label>
                                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px;">
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" name="treatment_plan" value="Surgery" style="margin-right: 8px;"> Surgery
                                        </label>
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" name="treatment_plan" value="Radiation" style="margin-right: 8px;"> Radiation
                                        </label>
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" name="treatment_plan" value="Chemotherapy" style="margin-right: 8px;"> Chemotherapy
                                        </label>
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" name="treatment_plan" value="Targeted Therapy" style="margin-right: 8px;"> Targeted Therapy
                                        </label>
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" name="treatment_plan" value="Immunotherapy" style="margin-right: 8px;"> Immunotherapy
                                        </label>
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" name="treatment_plan" value="Monitoring" style="margin-right: 8px;"> Monitoring Only
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="treatment_urgency" class="form-label">Treatment Urgency:</label>
                                    <select id="treatment_urgency" name="treatment_urgency" class="form-control">
                                        <option value="">-- Select urgency --</option>
                                        <option value="Immediate">🚨 Immediate (Within 1 week)</option>
                                        <option value="Urgent">⚠️ Urgent (Within 2-4 weeks)</option>
                                        <option value="Standard">📅 Standard (Within 1-3 months)</option>
                                        <option value="Non-urgent">✅ Non-urgent (As needed)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Section 3: Clinical Notes -->
                            <div class="form-section" style="margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #e0e0e0;">
                                <h4 style="color: #333; margin-bottom: 15px;">📝 Clinical Notes & Recommendations</h4>
                                
                                <div class="form-group">
                                    <label for="clinical_findings" class="form-label"><strong>Clinical Findings:</strong></label>
                                    <textarea 
                                        id="clinical_findings" 
                                        name="clinical_findings" 
                                        class="form-control" 
                                        rows="4" 
                                        placeholder="Describe key clinical findings that support your diagnosis..."
                                    ></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="recommendations" class="form-label"><strong>Clinical Recommendations:</strong></label>
                                    <textarea 
                                        id="recommendations" 
                                        name="recommendations" 
                                        class="form-control" 
                                        rows="4" 
                                        placeholder="Provide specific recommendations for patient management and follow-up..."
                                    ></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="follow_up_date" class="form-label">Recommended Follow-up Date:</label>
                                    <input 
                                        type="date" 
                                        id="follow_up_date" 
                                        name="follow_up_date" 
                                        class="form-control"
                                    >
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="form-actions" style="display: flex; gap: 10px; justify-content: space-between;">
                                <div>
                                    <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 16px;">
                                        ✅ Save Outcome & Diagnosis
                                    </button>
                                </div>
                                <div style="display: flex; gap: 10px;">
                                    <button type="button" class="btn btn-secondary" onclick="window.print()" style="padding: 12px 20px;">
                                        🖨️ Print Report
                                    </button>
                                    <a href="/CANCER/index.php?page=doctor-dashboard" class="btn btn-tertiary" style="padding: 12px 20px; text-decoration: none;">
                                        ← Back to Dashboard
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                // Show/hide cancer details based on diagnosis selection
                document.getElementById('final_diagnosis').addEventListener('change', function() {
                    const cancerDetailsSection = document.getElementById('cancerDetailsSection');
                    if (this.value === 'Malignant') {
                        cancerDetailsSection.style.display = 'block';
                    } else {
                        cancerDetailsSection.style.display = 'none';
                    }
                });

                // Handle form submission with AJAX
                document.getElementById('outcomeForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '⏳ Saving...';
                    
                    // Collect form data
                    const formData = new FormData(this);
                    
                    // Send AJAX request
                    fetch('/CANCER/index.php?page=api-outcome-action', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message and redirect
                            alert('✅ Outcome recorded successfully!\n\nRedirecting to dashboard...');
                            window.location.href = '/CANCER/index.php?page=doctor-dashboard';
                        } else {
                            // Show error message
                            alert('❌ Error: ' + (data.message || 'Failed to save outcome'));
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('❌ An error occurred while saving. Please try again.');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
                });

                // Load historical insights
                document.addEventListener('DOMContentLoaded', function() {
                    loadHistoricalInsights();
                });

                function loadHistoricalInsights() {
                    const assessmentId = <?php echo intval($assessment['id']); ?>;
                    const insightsDiv = document.getElementById('historicalInsights');
                    
                    fetch('/CANCER/index.php?page=api-historical-insights&action=full-insights&assessment_id=' + assessmentId)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                displayHistoricalInsights(data.data);
                            } else {
                                insightsDiv.innerHTML = '<p style="color: orange;">⚠️ Could not load historical insights: ' + (data.message || 'Unknown error') + '</p>';
                            }
                        })
                        .catch(error => {
                            console.error('Error loading insights:', error);
                            insightsDiv.innerHTML = '<p style="color: red;">❌ Error loading historical data: ' + error.message + '</p>';
                        });
                }

                function displayHistoricalInsights(data) {
                    const insightsDiv = document.getElementById('historicalInsights');
                    let html = '';

                    // Check if we have any data
                    if (!data || (Object.keys(data).length === 0)) {
                        insightsDiv.innerHTML = '<p style="color: orange;">⚠️ No historical data available yet. More assessments needed.</p>';
                        return;
                    }

                    // Recommendation Section
                    if (data.recommendation) {
                        const rec = data.recommendation;
                        const confColor = rec.confidence === 'high' ? '#4caf50' : '#ff9800';
                        html += `
                            <div style="background: ${confColor}22; border-left: 4px solid ${confColor}; padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: left;">
                                <h4 style="margin-top: 0; color: ${confColor};">💡 Evidence-Based Recommendation</h4>
                                <p><strong>${rec.message}</strong></p>
                                <p style="font-size: 12px; color: #666;">Based on ${rec.data_points} similar historical cases</p>
                            </div>
                        `;
                    }

                    // Cohort Statistics
                    if (data.cohort_stats && data.cohort_stats.total_patients > 0) {
                        const stats = data.cohort_stats;
                        html += `
                            <div style="background: #e3f2fd; padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: left;">
                                <h4 style="margin-top: 0; color: #1976d2;">📈 Similar Patient Cohort (${stats.total_patients} cases)</h4>
                                <table style="width: 100%; font-size: 13px;">
                                    <tr>
                                        <td><strong>Malignancy Rate:</strong></td>
                                        <td style="text-align: right; color: #d32f2f;"><strong>${stats.malignancy_rate}%</strong></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Average Risk Score:</strong></td>
                                        <td style="text-align: right;">${stats.avg_risk_score}/100</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Avg Days to Diagnosis:</strong></td>
                                        <td style="text-align: right;">${Math.round(stats.avg_days_to_diagnosis)} days</td>
                                    </tr>
                                </table>
                            </div>
                        `;
                    }

                    // Risk Level Accuracy
                    if (data.risk_accuracy) {
                        const accuracy = data.risk_accuracy;
                        html += `
                            <div style="background: #f3e5f5; padding: 15px; margin-bottom: 20px; border-radius: 5px; text-align: left;">
                                <h4 style="margin-top: 0; color: #7b1fa2;">🎯 Risk Assessment Accuracy</h4>
                                <p><strong>${accuracy.risk_level} Risk:</strong> ${accuracy.total_cases} historical cases</p>
                                <p style="color: #d32f2f;"><strong>Actual Malignancy Rate:</strong> ${accuracy.actual_malignancy_rate}%</p>
                                <p style="font-size: 12px; color: #666;">${accuracy.expected_outcome}</p>
                            </div>
                        `;
                    }

                    // Similar Cases
                    if (data.similar_cases && data.similar_cases.length > 0) {
                        html += `<div style="text-align: left; margin-top: 20px;">
                                    <h4 style="color: #1976d2;">👥 Similar Historical Cases (${data.similar_cases.length})</h4>
                                    <table style="width: 100%; font-size: 12px; border-collapse: collapse;">
                                        <tr style="background: #f0f0f0; border-bottom: 2px solid #ddd;">
                                            <th style="padding: 8px; text-align: left;">Date</th>
                                            <th style="padding: 8px; text-align: left;">Risk</th>
                                            <th style="padding: 8px; text-align: left;">Score</th>
                                            <th style="padding: 8px; text-align: left;">Diagnosis</th>
                                            <th style="padding: 8px; text-align: left;">Treatment</th>
                                        </tr>`;
                        
                        data.similar_cases.forEach(case_ => {
                            const diagColor = case_.final_diagnosis === 'Malignant' ? '#d32f2f' : '#4caf50';
                            html += `<tr style="border-bottom: 1px solid #eee;">
                                        <td style="padding: 8px;">${case_.assessment_date.substring(0,10)}</td>
                                        <td style="padding: 8px;">${case_.risk_level}</td>
                                        <td style="padding: 8px;">${case_.risk_score}/100</td>
                                        <td style="padding: 8px; color: ${diagColor}; font-weight: bold;">${case_.final_diagnosis || '—'}</td>
                                        <td style="padding: 8px; font-size: 11px;">${case_.treatment_plan || '—'}</td>
                                    </tr>`;
                        });
                        html += `</table></div>`;
                    }

                    insightsDiv.innerHTML = html || '<p>No historical data available yet</p>';
                }
                </script>
