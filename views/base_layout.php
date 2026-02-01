<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ICDSS - Throat Cancer Risk Assessment System</title>
    <link rel="stylesheet" href="/CANCER/assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS Throat Cancer Risk Assessment
                </h1>
            </div>
            <div class="navbar-user">
                <span class="user-info">
                    Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></strong> 
                    (<em><?php echo htmlspecialchars($_SESSION['user_role'] ?? ''); ?></em>)
                </span>
                <a href="/CANCER/logout.php" class="btn-logout">Logout</a>
            </div>
        </nav>

        <!-- Main Content Area -->
        <div class="main-content">
            <!-- Sidebar Navigation (for future expansion) -->
            <aside class="sidebar">
                <div class="nav-section">
                    <h3>Navigation</h3>
                    <ul class="nav-list">
                        <li><a href="/CANCER/dashboard.php" class="nav-link active">Dashboard</a></li>
                        <li><a href="/CANCER/new-assessment.php" class="nav-link">New Assessment</a></li>
                        <li><a href="/CANCER/patient-search.php" class="nav-link">Patient History</a></li>
                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                            <li><a href="/CANCER/admin-dashboard.php" class="nav-link">Admin Panel</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </aside>

            <!-- Content Panel -->
            <section class="content-panel">
                <!-- Content will be inserted here by specific pages -->
            </section>
        </div>
    </div>

    <script src="/CANCER/assets/js/form_validation.js"></script>
</body>
</html>
