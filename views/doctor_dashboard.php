<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/db_config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard - ICDSS</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - Throat Cancer Risk Assessment
                </h1>
            </div>
            <div class="navbar-user">
                <span class="user-info">
                    Welcome, <strong><?php echo htmlspecialchars($current_user['full_name'] ?? 'Doctor'); ?></strong>
                </span>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-logout">Logout</a>
            </div>
        </nav>

        <div class="main-content">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <div class="nav-section">
                    <h3>Quick Access</h3>
                    <ul class="nav-list">
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=doctor-dashboard" class="nav-link active">Dashboard</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=patient-assessment" class="nav-link">New Assessment</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=patient-search" class="nav-link">Patient History</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <div class="dashboard-header">
                    <h2>Doctor Dashboard</h2>
                    <p class="subtitle">Patient Risk Assessment & Clinical Decision Support</p>
                </div>

                <!-- Quick Stats Row -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-content">
                            <p class="stat-label">Total Assessments</p>
                            <p class="stat-value"><?php echo $total_assessments ?? '0'; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⚠️</div>
                        <div class="stat-content">
                            <p class="stat-label">High Risk Cases</p>
                            <p class="stat-value" style="color: #d32f2f;"><?php echo $high_risk_count ?? '0'; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <p class="stat-label">Patients Monitored</p>
                            <p class="stat-value"><?php echo $total_patients ?? '0'; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">📅</div>
                        <div class="stat-content">
                            <p class="stat-label">This Month</p>
                            <p class="stat-value"><?php echo $month_assessments ?? '0'; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Pending Outcomes Panel -->
                <div class="panel panel-warning" style="margin-bottom: 30px; border-left: 4px solid #ff9800; background-color: #fffbf0; box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);">
                    <div class="panel-header" style="background-color: #fff3e0; border-bottom: 2px solid #ff9800;">
                        <h3 style="margin: 0; color: #f57c00;">⏳ Assessments Pending Outcome Recording</h3>
                        <p class="panel-description" style="margin: 5px 0 0 0; color: #d84315;">Assessments awaiting your diagnosis and treatment plan</p>
                    </div>
                    <div class="panel-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Assessment Date</th>
                                    <th>Risk Level</th>
                                    <th>Risk Score</th>
                                    <th>Days Pending</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($pending_outcomes) && !empty($pending_outcomes)): ?>
                                    <?php foreach ($pending_outcomes as $assessment): ?>
                                        <?php 
                                            $assessmentDate = strtotime($assessment['assessment_date']);
                                            $daysPending = intval((time() - $assessmentDate) / 86400);
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></strong>
                                            </td>
                                            <td><?php echo date('M d, Y H:i', strtotime($assessment['assessment_date'])); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($assessment['risk_level'] ?? 'low'); ?>">
                                                    <?php echo htmlspecialchars($assessment['risk_level'] ?? 'Unknown'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($assessment['risk_score'] ?? 0, 1); ?>/100</td>
                                            <td>
                                                <span style="color: <?php echo $daysPending > 3 ? '#d32f2f' : '#ff9800'; ?>; font-weight: bold;">
                                                    <?php echo $daysPending; ?> day<?php echo $daysPending !== 1 ? 's' : ''; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/index.php?page=assessment-results&id=<?php echo $assessment['id']; ?>" class="btn btn-small btn-primary">
                                                    Record Outcome
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-success" style="padding: 20px;">
                                            ✅ All assessments have outcomes recorded! Great work!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Two-Column Layout: Assessment Form + Recent Cases -->
                <div class="dashboard-grid">
                    <!-- Column 1: Quick Assessment Form -->
                    <div class="panel panel-primary">
                        <div class="panel-header">
                            <h3>Quick Assessment</h3>
                            <p class="panel-description">Start a new patient risk assessment</p>
                        </div>
                        <div class="panel-body">
                            <form id="quickAssessmentForm" action="<?php echo BASE_URL; ?>/submit_assessment.php" method="POST">
                                <!-- Patient Information -->
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="first_name" class="form-label">First Name:</label>
                                        <input 
                                            type="text" 
                                            id="first_name" 
                                            name="first_name" 
                                            class="form-control" 
                                            placeholder="Patient first name"
                                            required
                                        >
                                    </div>
                                    <div class="form-group">
                                        <label for="last_name" class="form-label">Last Name:</label>
                                        <input 
                                            type="text" 
                                            id="last_name" 
                                            name="last_name" 
                                            class="form-control" 
                                            placeholder="Patient last name"
                                            required
                                        >
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="date_of_birth" class="form-label">Date of Birth:</label>
                                        <input 
                                            type="date" 
                                            id="date_of_birth" 
                                            name="date_of_birth" 
                                            class="form-control"
                                            required
                                        >
                                    </div>
                                    <div class="form-group">
                                        <label for="gender" class="form-label">Gender:</label>
                                        <select id="gender" name="gender" class="form-control" required>
                                            <option value="">Select...</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Other">Other</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Symptoms Section -->
                                <div class="form-section-header">
                                    <h4>Symptoms</h4>
                                </div>

                                <div class="form-group">
                                    <label for="sore_throat_duration" class="form-label">Persistent Sore Throat Duration (weeks):</label>
                                    <input 
                                        type="number" 
                                        id="sore_throat_duration" 
                                        name="sore_throat_duration" 
                                        class="form-control" 
                                        min="0" 
                                        placeholder="Duration in weeks"
                                    >
                                </div>

                                <div class="form-group">
                                    <label class="form-label checkbox-group">
                                        <input type="checkbox" name="voice_changes" value="1">
                                        Voice Changes (hoarseness, pitch changes)
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="form-label checkbox-group">
                                        <input type="checkbox" name="difficulty_swallowing" value="1">
                                        Difficulty Swallowing (dysphagia)
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="form-label checkbox-group">
                                        <input type="checkbox" name="neck_lump" value="1">
                                        Neck Lump (palpable mass)
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label class="form-label checkbox-group">
                                        <input type="checkbox" name="weight_loss" value="1">
                                        Unexplained Weight Loss
                                    </label>
                                </div>

                                <div id="weight_loss_details" style="display: none;">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="weight_loss_percentage" class="form-label">Weight Loss (%):</label>
                                            <input 
                                                type="number" 
                                                id="weight_loss_percentage" 
                                                name="weight_loss_percentage" 
                                                class="form-control" 
                                                step="0.1" 
                                                min="0" 
                                                max="100"
                                            >
                                        </div>
                                        <div class="form-group">
                                            <label for="weight_loss_weeks" class="form-label">Duration (weeks):</label>
                                            <input 
                                                type="number" 
                                                id="weight_loss_weeks" 
                                                name="weight_loss_weeks" 
                                                class="form-control" 
                                                min="0"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Lifestyle Factors -->
                                <div class="form-section-header">
                                    <h4>Lifestyle Factors</h4>
                                </div>

                                <div class="form-group">
                                    <label for="smoking_status" class="form-label">Smoking Status:</label>
                                    <select id="smoking_status" name="smoking_status" class="form-control">
                                        <option value="">Select...</option>
                                        <option value="never">Never Smoked</option>
                                        <option value="former">Former Smoker</option>
                                        <option value="current">Current Smoker</option>
                                    </select>
                                </div>

                                <div id="smoking_years_div" style="display: none;">
                                    <div class="form-group">
                                        <label for="smoking_years" class="form-label">Smoking Duration (years):</label>
                                        <input 
                                            type="number" 
                                            id="smoking_years" 
                                            name="smoking_years" 
                                            class="form-control" 
                                            min="0" 
                                            max="80"
                                        >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="alcohol_consumption" class="form-label">Alcohol Consumption:</label>
                                    <select id="alcohol_consumption" name="alcohol_consumption" class="form-control">
                                        <option value="">Select...</option>
                                        <option value="none">None</option>
                                        <option value="mild">Mild (1-2 drinks/week)</option>
                                        <option value="moderate">Moderate (3-7 drinks/week)</option>
                                        <option value="heavy">Heavy (8+ drinks/week)</option>
                                    </select>
                                </div>

                                <!-- Medical History -->
                                <div class="form-section-header">
                                    <h4>Medical History</h4>
                                </div>

                                <div class="form-group">
                                    <label for="hpv_status" class="form-label">HPV Status:</label>
                                    <select id="hpv_status" name="hpv_status" class="form-control">
                                        <option value="unknown">Unknown</option>
                                        <option value="positive">Positive</option>
                                        <option value="negative">Negative</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label class="form-label checkbox-group">
                                        <input type="checkbox" name="family_cancer_history" value="1">
                                        Family History of Cancer
                                    </label>
                                </div>

                                <div id="family_cancer_details" style="display: none;">
                                    <div class="form-group">
                                        <label for="family_cancer_type" class="form-label">Cancer Type(s):</label>
                                        <input 
                                            type="text" 
                                            id="family_cancer_type" 
                                            name="family_cancer_type" 
                                            class="form-control" 
                                            placeholder="e.g., Throat, Lung, Breast"
                                        >
                                    </div>
                                </div>

                                <!-- Lab Indicators -->
                                <div class="form-section-header">
                                    <h4>Lab Indicators</h4>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="hemoglobin_level" class="form-label">Hemoglobin (g/dL):</label>
                                        <input 
                                            type="number" 
                                            id="hemoglobin_level" 
                                            name="hemoglobin_level" 
                                            class="form-control" 
                                            step="0.1" 
                                            min="0" 
                                            placeholder="Normal: 13.5-17.5"
                                        >
                                    </div>
                                    <div class="form-group">
                                        <label for="lymphocyte_count" class="form-label">Lymphocyte Count (K/uL):</label>
                                        <input 
                                            type="number" 
                                            id="lymphocyte_count" 
                                            name="lymphocyte_count" 
                                            class="form-control" 
                                            step="0.1" 
                                            min="0" 
                                            placeholder="Normal: 1.0-4.8"
                                        >
                                    </div>
                                </div>

                                <!-- Clinical Notes -->
                                <div class="form-group">
                                    <label for="clinical_notes" class="form-label">Clinical Notes:</label>
                                    <textarea 
                                        id="clinical_notes" 
                                        name="clinical_notes" 
                                        class="form-control" 
                                        rows="3" 
                                        placeholder="Additional observations, findings..."
                                    ></textarea>
                                </div>

                                <!-- Submit Buttons -->
                                <div class="form-actions">
                                    <button type="submit" name="action" value="assess" class="btn btn-primary">
                                        Calculate Risk Assessment
                                    </button>
                                    <button type="reset" class="btn btn-secondary">
                                        Clear Form
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Column 2: Recent Assessments -->
                    <div class="panel panel-secondary">
                        <div class="panel-header">
                            <h3>Recent Assessments</h3>
                            <p class="panel-description">Latest patient evaluations</p>
                        </div>
                        <div class="panel-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Patient Name</th>
                                        <th>Assessment Date</th>
                                        <th>Risk Level</th>
                                        <th>Risk Score</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($recent_assessments) && !empty($recent_assessments)): ?>
                                        <?php foreach ($recent_assessments as $assessment): ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></strong>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($assessment['assessment_date'])); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo strtolower($assessment['risk_level'] ?? 'low'); ?>">
                                                        <?php echo htmlspecialchars($assessment['risk_level'] ?? 'Not Assessed'); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <strong><?php echo number_format($assessment['risk_score'] ?? 0, 1); ?>/100</strong>
                                                </td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>/index.php?page=assessment-results&id=<?php echo $assessment['id']; ?>" class="link-action">
                                                        View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No assessments yet. Start with a new assessment.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="<?php echo BASE_URL; ?>/assets/js/form_validation.js"></script>
    <script src="<?php echo BASE_URL; ?>/assets/js/dashboard.js"></script>
</body>
</html>
