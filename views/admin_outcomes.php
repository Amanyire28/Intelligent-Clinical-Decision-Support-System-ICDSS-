<?php
/**
 * ADMIN OUTCOMES MANAGEMENT VIEW
 * Record patient diagnosis, treatment, and follow-up outcomes
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../config/db_config.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Patient Outcomes - ICDSS</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - Patient Outcomes Management
                </h1>
            </div>
            <div class="navbar-user">
                <span class="user-info">
                    Welcome, <strong><?php echo htmlspecialchars($current_user['full_name'] ?? 'Admin'); ?></strong>
                </span>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-logout">Logout</a>
            </div>
        </nav>

        <div class="main-content">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <div class="nav-section">
                    <h3>Admin Menu</h3>
                    <ul class="nav-list">
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-dashboard" class="nav-link">Dashboard</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-users" class="nav-link">User Management</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-assessments" class="nav-link">Assessments</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-outcomes" class="nav-link active">Outcomes</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <div class="dashboard-header">
                    <h2>Patient Outcomes Management</h2>
                    <p class="subtitle">Record diagnosis, treatment, and follow-up outcomes for population learning</p>
                </div>

                <!-- Outcome Statistics -->
                <?php if ($outcomeStats): ?>
                    <div class="stats-row">
                        <div class="stat-card">
                            <div class="stat-icon">📊</div>
                            <div class="stat-content">
                                <p class="stat-label">Outcomes Recorded</p>
                                <p class="stat-value"><?php echo $outcomeStats['total_patients'] ?? '0'; ?></p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">🚨</div>
                            <div class="stat-content">
                                <p class="stat-label">Malignancy Rate</p>
                                <p class="stat-value" style="color: #d32f2f;">
                                    <?php echo number_format($outcomeStats['malignancy_count'] ?? 0); ?> 
                                    (<?php echo number_format($outcomeStats['malignancy_count'] / max($outcomeStats['total_patients'], 1) * 100, 1); ?>%)
                                </p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">✅</div>
                            <div class="stat-content">
                                <p class="stat-label">Survival Rate</p>
                                <p class="stat-value" style="color: #4caf50;">
                                    <?php echo number_format($outcomeStats['survival_rate'] ?? 0, 1); ?>%
                                </p>
                            </div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-icon">📅</div>
                            <div class="stat-content">
                                <p class="stat-label">Avg Risk Score</p>
                                <p class="stat-value"><?php echo number_format($outcomeStats['avg_risk_score'] ?? 0, 1); ?>/100</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Pending Outcomes Table -->
                <div class="panel panel-primary">
                    <div class="panel-header">
                        <h3>Assessments Pending Outcome Recording</h3>
                        <p class="panel-description">Click to record diagnosis and treatment outcomes</p>
                    </div>
                    <div class="panel-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Assessment Date</th>
                                    <th>Doctor</th>
                                    <th>Risk Level</th>
                                    <th>Risk Score</th>
                                    <th>Days Pending</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($pendingOutcomes)): ?>
                                    <?php foreach ($pendingOutcomes as $assessment): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></strong>
                                            </td>
                                            <td><?php echo date('M d, Y H:i', strtotime($assessment['assessment_date'])); ?></td>
                                            <td><?php echo htmlspecialchars($assessment['doctor_name']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo strtolower($assessment['risk_level'] ?? 'low'); ?>">
                                                    <?php echo htmlspecialchars($assessment['risk_level'] ?? 'Unknown'); ?>
                                                </span>
                                            </td>
                                            <td><?php echo number_format($assessment['risk_score'] ?? 0, 1); ?></td>
                                            <td>
                                                <?php 
                                                    $assessmentDate = strtotime($assessment['assessment_date']);
                                                    $daysPending = intval((time() - $assessmentDate) / 86400);
                                                    echo $daysPending . ' days';
                                                ?>
                                            </td>
                                            <td>
                                                <button class="btn btn-small btn-primary" onclick="openOutcomeModal(<?php echo $assessment['id']; ?>)">
                                                    Record Outcome
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No pending outcomes. All assessments have been recorded!</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Outcome Recording Modal -->
    <div id="outcomeModal" class="modal" style="display: none;">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h3>Record Patient Outcome</h3>
                <span class="modal-close" onclick="closeOutcomeModal()">&times;</span>
            </div>
            <form id="outcomeForm" method="POST" action="<?php echo BASE_URL; ?>/index.php?page=api-outcome-action">
                <input type="hidden" name="action" value="record">
                <input type="hidden" name="assessment_id" id="outcomeAssessmentId" value="">

                <div class="form-section">
                    <h4>Diagnosis Information</h4>
                    
                    <div class="form-group">
                        <label for="final_diagnosis" class="form-label">Final Diagnosis:</label>
                        <select id="final_diagnosis" name="final_diagnosis" class="form-control" required>
                            <option value="">Select diagnosis...</option>
                            <option value="Malignant">Malignant (Cancer)</option>
                            <option value="Benign">Benign Condition</option>
                            <option value="Pending">Pending Further Investigation</option>
                            <option value="Unknown">Unknown/Inconclusive</option>
                        </select>
                    </div>

                    <div id="cancerDetailsDiv" style="display: none;">
                        <div class="form-group">
                            <label for="cancer_type" class="form-label">Cancer Type:</label>
                            <select id="cancer_type" name="cancer_type" class="form-control">
                                <option value="">Select type...</option>
                                <option value="Squamous Cell Carcinoma">Squamous Cell Carcinoma</option>
                                <option value="Adenocarcinoma">Adenocarcinoma</option>
                                <option value="Salivary Gland Cancer">Salivary Gland Cancer</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cancer_stage" class="form-label">Cancer Stage (TNM):</label>
                            <select id="cancer_stage" name="cancer_stage" class="form-control">
                                <option value="">Select stage...</option>
                                <option value="Stage 1">Stage 1</option>
                                <option value="Stage 2">Stage 2</option>
                                <option value="Stage 2A">Stage 2A</option>
                                <option value="Stage 2B">Stage 2B</option>
                                <option value="Stage 3">Stage 3</option>
                                <option value="Stage 3A">Stage 3A</option>
                                <option value="Stage 3B">Stage 3B</option>
                                <option value="Stage 4">Stage 4</option>
                                <option value="Stage 4A">Stage 4A</option>
                                <option value="Stage 4B">Stage 4B</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Treatment Information</h4>
                    
                    <div class="form-group">
                        <label for="treatment_type" class="form-label">Treatment Type:</label>
                        <select id="treatment_type" name="treatment_type" class="form-control">
                            <option value="">Select treatment...</option>
                            <option value="Surgery">Surgery</option>
                            <option value="Chemotherapy">Chemotherapy</option>
                            <option value="Radiation">Radiation Therapy</option>
                            <option value="Combined Modality">Combined Modality (Surgery + Chemo/Radiation)</option>
                            <option value="Targeted Therapy">Targeted Therapy</option>
                            <option value="Watchful Waiting">Watchful Waiting/Monitoring</option>
                            <option value="Palliative">Palliative Care</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="outcome_date" class="form-label">Outcome/Diagnosis Date:</label>
                        <input type="date" id="outcome_date" name="outcome_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Follow-up Status</h4>
                    
                    <div class="form-group">
                        <label for="follow_up_status" class="form-label">Follow-up Status:</label>
                        <select id="follow_up_status" name="follow_up_status" class="form-control">
                            <option value="">Select status...</option>
                            <option value="NED">NED - No Evidence of Disease</option>
                            <option value="Recurrence">Recurrence Detected</option>
                            <option value="Progressive">Progressive Disease</option>
                            <option value="Unknown">Unknown/Not Yet Determined</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="survival_status" class="form-label">Survival Status:</label>
                        <select id="survival_status" name="survival_status" class="form-control">
                            <option value="Alive">Alive</option>
                            <option value="Deceased">Deceased</option>
                        </select>
                    </div>

                    <div id="yearsSurvivedDiv" style="display: none;">
                        <div class="form-group">
                            <label for="years_survived" class="form-label">Years Survived:</label>
                            <input type="number" id="years_survived" name="years_survived" class="form-control" step="0.1" min="0" placeholder="Years">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h4>Additional Notes</h4>
                    
                    <div class="form-group">
                        <label for="notes" class="form-label">Clinical Notes:</label>
                        <textarea id="notes" name="notes" class="form-control" rows="3" placeholder="Any additional clinical information..."></textarea>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Outcome</button>
                    <button type="button" class="btn btn-secondary" onclick="closeOutcomeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Show/hide cancer details based on diagnosis
        document.getElementById('final_diagnosis')?.addEventListener('change', function() {
            const cancerDiv = document.getElementById('cancerDetailsDiv');
            cancerDiv.style.display = this.value === 'Malignant' ? 'block' : 'none';
        });

        // Show/hide years survived based on survival status
        document.getElementById('survival_status')?.addEventListener('change', function() {
            const yearsDiv = document.getElementById('yearsSurvivedDiv');
            yearsDiv.style.display = this.value === 'Deceased' ? 'block' : 'none';
        });

        function openOutcomeModal(assessmentId) {
            document.getElementById('outcomeAssessmentId').value = assessmentId;
            document.getElementById('outcomeForm').reset();
            document.getElementById('cancerDetailsDiv').style.display = 'none';
            document.getElementById('yearsSurvivedDiv').style.display = 'none';
            document.getElementById('outcomeModal').style.display = 'flex';
        }

        function closeOutcomeModal() {
            document.getElementById('outcomeModal').style.display = 'none';
        }

        document.getElementById('outcomeForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    alert('Outcome recorded successfully!');
                    closeOutcomeModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to record outcome'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            }
        });

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('outcomeModal');
            if (event.target == modal) {
                closeOutcomeModal();
            }
        });
    </script>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/modal.css">
</body>
</html>
