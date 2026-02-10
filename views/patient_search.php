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
    <title>Patient History - ICDSS</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - Patient History & Search
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
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=doctor-dashboard" class="nav-link">Dashboard</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=patient-assessment" class="nav-link">New Assessment</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=patient-search" class="nav-link active">Patient History</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <div class="dashboard-header">
                    <h2>Patient History</h2>
                    <p class="subtitle">Search and view patient assessment history</p>
                </div>

                <!-- Search Panel -->
                <div class="panel panel-primary">
                    <div class="panel-header">
                        <h3>Search Patients</h3>
                        <p class="panel-description">Find patients by name or medical record number</p>
                    </div>
                    <div class="panel-body">
                        <div class="search-form">
                            <div class="form-group">
                                <label for="search_term" class="form-label">Search by Name or MRN:</label>
                                <input 
                                    type="text" 
                                    id="search_term" 
                                    name="search_term" 
                                    class="form-control" 
                                    placeholder="Enter patient name or medical record number..."
                                    autocomplete="off"
                                >
                                <div id="searchResults" class="search-results-dropdown"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patients Table -->
                <div class="panel panel-secondary">
                    <div class="panel-header">
                        <h3>Recent Assessments</h3>
                        <p class="panel-description">Latest patient evaluations and results</p>
                    </div>
                    <div class="panel-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>MRN</th>
                                    <th>Last Assessment</th>
                                    <th>Risk Level</th>
                                    <th>Risk Score</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($recent_assessments) && !empty($recent_assessments)): ?>
                                    <?php foreach ($recent_assessments as $assessment): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($assessment['medical_record_number'] ?? 'N/A'); ?></td>
                                            <td><?php echo date('M d, Y H:i', strtotime($assessment['assessment_date'])); ?></td>
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
                                        <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
                                            No assessments found. Start by creating a new assessment!
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="panel panel-info">
                    <div class="panel-header">
                        <h3>How to Use</h3>
                    </div>
                    <div class="panel-body">
                        <ul style="list-style-position: inside; line-height: 1.8;">
                            <li><strong>Search:</strong> Type the patient's first/last name or their medical record number (MRN) in the search box</li>
                            <li><strong>View History:</strong> Click on a patient to see all their assessments</li>
                            <li><strong>Assessment Details:</strong> Click "View Details" to see the full assessment and risk analysis</li>
                            <li><strong>Export:</strong> Use the export button to download patient records (coming in Phase 3)</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        // Patient search functionality
        document.getElementById('search_term').addEventListener('input', function(e) {
            const searchTerm = this.value.trim();
            
            if (searchTerm.length < 2) {
                document.getElementById('searchResults').innerHTML = '';
                return;
            }
            
            // AJAX search request
            fetch(`/CANCER/index.php?page=patient-search&action=search&term=${encodeURIComponent(searchTerm)}`)
                .then(response => response.json())
                .then(data => {
                    const resultsDiv = document.getElementById('searchResults');
                    
                    if (data.results && data.results.length > 0) {
                        resultsDiv.innerHTML = data.results.map(patient => `
                            <div class="search-result-item">
                                <strong>${patient.first_name} ${patient.last_name}</strong>
                                <small>MRN: ${patient.medical_record_number || 'N/A'}</small>
                            </div>
                        `).join('');
                    } else {
                        resultsDiv.innerHTML = '<div class="search-result-item">No patients found</div>';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    document.getElementById('searchResults').innerHTML = '<div class="search-result-item">Error searching patients</div>';
                });
        });
    </script>
</body>
</html>
