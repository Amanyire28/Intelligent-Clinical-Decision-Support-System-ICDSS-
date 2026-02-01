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

                <!-- Action Panel -->
                <div class="panel panel-action">
                    <div class="panel-header">
                        <h3>Next Steps</h3>
                    </div>
                    <div class="panel-body">
                        <form method="POST" action="/CANCER/controllers/ActionController.php" id="actionForm">
                            <input type="hidden" name="assessment_id" value="<?php echo htmlspecialchars($assessment['id']); ?>">
                            
                            <div class="form-group">
                                <label for="action_type" class="form-label">Clinical Action:</label>
                                <select id="action_type" name="action_type" class="form-control" required>
                                    <option value="">Select action...</option>
                                    <option value="monitoring">Routine Monitoring</option>
                                    <option value="additional_tests">Additional Tests Needed</option>
                                    <option value="referred">Refer to Specialist</option>
                                    <option value="cleared">Cleared - No Further Action</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="action_notes" class="form-label">Notes:</label>
                                <textarea 
                                    id="action_notes" 
                                    name="action_notes" 
                                    class="form-control" 
                                    rows="3" 
                                    placeholder="Document your clinical decision..."
                                ></textarea>
                            </div>

                            <div class="form-group">
                                <label for="follow_up_date" class="form-label">Follow-up Date (if applicable):</label>
                                <input 
                                    type="date" 
                                    id="follow_up_date" 
                                    name="follow_up_date" 
                                    class="form-control"
                                >
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Record Action</button>
                                <a href="/CANCER/doctor-dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
                                <button type="button" class="btn btn-tertiary" onclick="window.print()">Print Report</button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="/CANCER/assets/js/form_validation.js"></script>
</body>
</html>
