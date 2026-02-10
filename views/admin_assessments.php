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
    <title>View All Assessments - ICDSS Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - All Assessments
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
                    <h3>Admin Panel</h3>
                    <ul class="nav-list">
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-dashboard" class="nav-link">Dashboard</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-users" class="nav-link">User Management</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-assessments" class="nav-link active">View Assessments</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-reports" class="nav-link">System Reports</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-config" class="nav-link">Configuration</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <div class="dashboard-header">
                    <h2>All Patient Assessments</h2>
                    <p class="subtitle">System-wide assessment records and results</p>
                </div>

                <!-- Filter and Search -->
                <div class="panel panel-secondary">
                    <div class="panel-header">
                        <h3>Filter & Search</h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="filter_risk_level">Risk Level:</label>
                                <select id="filter_risk_level" class="form-control">
                                    <option value="">All</option>
                                    <option value="Low">Low Risk</option>
                                    <option value="Moderate">Moderate Risk</option>
                                    <option value="High">High Risk</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="search_term">Search by Patient Name:</label>
                                <input type="text" id="search_term" class="form-control" placeholder="Enter patient name...">
                            </div>
                            <div class="form-group" style="padding-top: 25px;">
                                <button class="btn btn-secondary">Apply Filters</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Assessments Table -->
                <div class="panel panel-primary">
                    <div class="panel-header">
                        <h3>Assessment Records</h3>
                        <p class="panel-description">Complete history of all patient evaluations</p>
                    </div>
                    <div class="panel-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Patient Name</th>
                                    <th>Doctor</th>
                                    <th>Assessment Date</th>
                                    <th>Risk Level</th>
                                    <th>Risk Score</th>
                                    <th>Confidence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($assessments) && !empty($assessments)): ?>
                                    <?php foreach ($assessments as $assessment): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($assessment['first_name'] . ' ' . $assessment['last_name']); ?></strong>
                                            </td>
                                            <td><?php echo htmlspecialchars($assessment['doctor_name'] ?? 'Unknown'); ?></td>
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
                                                <div class="progress-bar" style="width: 100px; height: 20px;">
                                                    <div class="progress-fill" style="width: <?php echo $assessment['confidence_percentage'] ?? 0; ?>%">
                                                        <span class="progress-text" style="font-size: 12px;">
                                                            <?php echo number_format($assessment['confidence_percentage'] ?? 0, 0); ?>%
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?php echo BASE_URL; ?>/index.php?page=assessment-results&id=<?php echo $assessment['assessment_id']; ?>" class="link-action">
                                                    View Details
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 20px; color: #999;">
                                            No assessments found in the system yet.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="panel panel-info">
                    <div class="panel-header">
                        <h3>Assessment Statistics</h3>
                    </div>
                    <div class="panel-body">
                        <div class="stats-row">
                            <div class="stat-card">
                                <div class="stat-icon">📊</div>
                                <div class="stat-content">
                                    <p class="stat-label">Total Assessments</p>
                                    <p class="stat-value"><?php echo $stats_total_assessments ?? 0; ?></p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">📈</div>
                                <div class="stat-content">
                                    <p class="stat-label">This Month</p>
                                    <p class="stat-value"><?php echo $stats_this_month ?? 0; ?></p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">⚠️</div>
                                <div class="stat-content">
                                    <p class="stat-label">High Risk Cases</p>
                                    <p class="stat-value"><?php echo $stats_high_risk ?? 0; ?></p>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">✓</div>
                                <div class="stat-content">
                                    <p class="stat-label">Low Risk Cases</p>
                                    <p class="stat-value"><?php echo $stats_low_risk ?? 0; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
