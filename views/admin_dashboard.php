<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - ICDSS</title>
    <link rel="stylesheet" href="/CANCER/assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - Admin Dashboard
                </h1>
            </div>
            <div class="navbar-user">
                <span class="user-info">
                    Welcome, <strong><?php echo htmlspecialchars($current_user['full_name'] ?? 'Admin'); ?></strong>
                </span>
                <a href="/CANCER/logout.php" class="btn-logout">Logout</a>
            </div>
        </nav>

        <div class="main-content">
            <!-- Sidebar Navigation -->
            <aside class="sidebar">
                <div class="nav-section">
                    <h3>Admin Panel</h3>
                    <ul class="nav-list">
                        <li><a href="/CANCER/index.php?page=admin-dashboard" class="nav-link active">Dashboard</a></li>
                        <li><a href="/CANCER/index.php?page=admin-users" class="nav-link">User Management</a></li>
                        <li><a href="/CANCER/index.php?page=admin-assessments" class="nav-link">View Assessments</a></li>
                        <li><a href="/CANCER/index.php?page=admin-reports" class="nav-link">System Reports</a></li>
                        <li><a href="/CANCER/index.php?page=admin-config" class="nav-link">Configuration</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <div class="dashboard-header">
                    <h2>System Administration Dashboard</h2>
                    <p class="subtitle">Monitor system health, users, and assessments</p>
                </div>

                <!-- System Statistics Row -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-icon">📊</div>
                        <div class="stat-content">
                            <p class="stat-label">Total Assessments</p>
                            <p class="stat-value"><?php echo $total_assessments ?? '0'; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">👨‍⚕️</div>
                        <div class="stat-content">
                            <p class="stat-label">Active Doctors</p>
                            <p class="stat-value"><?php echo $active_doctors ?? '0'; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">👥</div>
                        <div class="stat-content">
                            <p class="stat-label">Registered Patients</p>
                            <p class="stat-value"><?php echo $total_patients ?? '0'; ?></p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">⚠️</div>
                        <div class="stat-content">
                            <p class="stat-label">High-Risk Cases</p>
                            <p class="stat-value" style="color: #d32f2f;"><?php echo $high_risk_cases ?? '0'; ?></p>
                        </div>
                    </div>
                </div>

                <!-- Admin Grid: Two Columns -->
                <div class="admin-grid">
                    <!-- Column 1: Risk Distribution & High-Risk Patients -->
                    <div class="panel panel-primary">
                        <div class="panel-header">
                            <h3>Risk Level Distribution</h3>
                            <p class="panel-description">Assessment breakdown by risk category</p>
                        </div>
                        <div class="panel-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Risk Level</th>
                                        <th>Count</th>
                                        <th>Percentage</th>
                                        <th>Avg Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($risk_statistics) && !empty($risk_statistics)): ?>
                                        <?php 
                                            $total_risk = array_sum(array_column($risk_statistics, 'count'));
                                            foreach ($risk_statistics as $stat):
                                                $percentage = $total_risk > 0 ? ($stat['count'] / $total_risk) * 100 : 0;
                                        ?>
                                            <tr>
                                                <td>
                                                    <span class="badge badge-<?php echo strtolower($stat['risk_level']); ?>">
                                                        <?php echo htmlspecialchars($stat['risk_level']); ?> Risk
                                                    </span>
                                                </td>
                                                <td><strong><?php echo $stat['count']; ?></strong></td>
                                                <td><?php echo number_format($percentage, 1); ?>%</td>
                                                <td><?php echo number_format($stat['average_score'] ?? 0, 2); ?>/100</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No assessment data available yet.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Column 2: High-Risk Patients Requiring Attention -->
                    <div class="panel panel-warning">
                        <div class="panel-header">
                            <h3>High-Risk Patients</h3>
                            <p class="panel-description">Patients requiring immediate attention</p>
                        </div>
                        <div class="panel-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Patient</th>
                                        <th>Risk Score</th>
                                        <th>Assessment Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($high_risk_patients) && !empty($high_risk_patients)): ?>
                                        <?php foreach ($high_risk_patients as $patient): ?>
                                            <tr class="highlight-warning">
                                                <td>
                                                    <strong><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></strong>
                                                </td>
                                                <td>
                                                    <strong style="color: #d32f2f;"><?php echo number_format($patient['risk_score'], 1); ?>/100</strong>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($patient['assessment_date'])); ?></td>
                                                <td>
                                                    <a href="/CANCER/assessment-details.php?id=<?php echo $patient['id']; ?>" class="link-action">
                                                        Review
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No high-risk patients.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Doctor Activity & User Management -->
                <div class="panel panel-secondary">
                    <div class="panel-header">
                        <h3>Registered Doctors</h3>
                        <a href="/CANCER/admin-users.php" class="link-action">Manage Users →</a>
                    </div>
                    <div class="panel-body">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Specialization</th>
                                    <th>License Number</th>
                                    <th>Assessments (Total)</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($doctors) && !empty($doctors)): ?>
                                    <?php foreach ($doctors as $doctor): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($doctor['full_name']); ?></strong>
                                                <br><small><?php echo htmlspecialchars($doctor['email']); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($doctor['specialization'] ?? 'General'); ?></td>
                                            <td><?php echo htmlspecialchars($doctor['license_number'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?php echo $doctor['assessment_count'] ?? '0'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge <?php echo $doctor['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                                    <?php echo $doctor['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="/CANCER/admin-edit-user.php?id=<?php echo $doctor['id']; ?>" class="link-action">Edit</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No doctors registered yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- System Configuration & Tools -->
                <div class="admin-grid">
                    <!-- Risk Model Configuration (Placeholder) -->
                    <div class="panel panel-secondary">
                        <div class="panel-header">
                            <h3>Risk Model Configuration</h3>
                            <p class="panel-description">System settings (Phase 4)</p>
                        </div>
                        <div class="panel-body">
                            <div class="placeholder-content">
                                <p class="text-muted">
                                    <strong>Feature Coming Soon:</strong> Configure risk scoring weights and thresholds
                                </p>
                                <ul class="placeholder-list">
                                    <li>Adjust factor weights</li>
                                    <li>Configure risk thresholds</li>
                                    <li>Test model parameters</li>
                                    <li>View scoring audit log</li>
                                </ul>
                                <button class="btn btn-secondary disabled">Configure (Phase 4)</button>
                            </div>
                        </div>
                    </div>

                    <!-- System Health & Logs -->
                    <div class="panel panel-secondary">
                        <div class="panel-header">
                            <h3>System Information</h3>
                        </div>
                        <div class="panel-body">
                            <div class="system-info">
                                <div class="info-row">
                                    <label>System Status:</label>
                                    <span class="badge badge-success">Operational</span>
                                </div>
                                <div class="info-row">
                                    <label>Database Status:</label>
                                    <span class="badge badge-success">Connected</span>
                                </div>
                                <div class="info-row">
                                    <label>Last Backup:</label>
                                    <span class="text-muted">Manual backup available</span>
                                </div>
                                <div class="info-row">
                                    <label>System Version:</label>
                                    <span class="text-muted">Phase 1 (UI/Frontend)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent System Activity Log -->
                <div class="panel panel-secondary">
                    <div class="panel-header">
                        <h3>Recent System Activity</h3>
                        <a href="/CANCER/admin-logs.php" class="link-action">View Full Log →</a>
                    </div>
                    <div class="panel-body">
                        <table class="data-table compact">
                            <thead>
                                <tr>
                                    <th>Timestamp</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>Resource</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($recent_logs) && !empty($recent_logs)): ?>
                                    <?php foreach ($recent_logs as $log): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y H:i', strtotime($log['created_at'])); ?></td>
                                            <td><?php echo htmlspecialchars($log['user_name'] ?? 'System'); ?></td>
                                            <td><?php echo htmlspecialchars($log['action']); ?></td>
                                            <td>
                                                <span class="badge badge-secondary">
                                                    <?php echo htmlspecialchars($log['resource_type'] ?? 'System'); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent activity.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="/CANCER/assets/js/admin.js"></script>
</body>
</html>
