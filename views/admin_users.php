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
    <title>User Management - ICDSS Admin</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            animation: fadeIn 0.3s ease-in;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 30px;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            animation: slideDown 0.3s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideDown {
            from { transform: translateY(-50px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .close-modal {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close-modal:hover {
            color: #000;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.3);
        }
        .form-buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        .alert {
            padding: 12px 20px;
            margin-bottom: 15px;
            border-radius: 4px;
            display: none;
        }
        .alert.alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            display: none;
        }
        .alert.alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            display: none;
        }
        .alert.show {
            display: block;
        }
        .required {
            color: red;
        }
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
        }
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #666;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
        }
        .tab-btn.active {
            color: #4CAF50;
            border-bottom-color: #4CAF50;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - User Management
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
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-users" class="nav-link active">User Management</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-assessments" class="nav-link">View Assessments</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-reports" class="nav-link">System Reports</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/index.php?page=admin-config" class="nav-link">Configuration</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <div class="dashboard-header">
                    <h2>User Management</h2>
                    <p class="subtitle">Manage doctors and system administrators</p>
                </div>

                <!-- Add New User Buttons -->
                <div style="margin-bottom: 20px; display: flex; gap: 10px;">
                    <button class="btn btn-primary" onclick="openAddUserModal('doctor')">+ Add New Doctor</button>
                    <button class="btn btn-success" onclick="openAddUserModal('admin')">+ Add New Admin</button>
                </div>

                <!-- Tabs for Doctors and Admins -->
                <div class="tabs">
                    <button class="tab-btn active" onclick="switchTab('doctors')">Doctors (<?php echo count($doctors); ?>)</button>
                    <button class="tab-btn" onclick="switchTab('admins')">System Admins (<?php echo count($admins); ?>)</button>
                </div>

                <!-- Doctors Table -->
                <div id="doctors" class="tab-content active">
                    <div class="panel panel-primary">
                        <div class="panel-header">
                            <h3>Active Doctors</h3>
                            <p class="panel-description">System physicians and medical staff</p>
                        </div>
                        <div class="panel-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Specialization</th>
                                        <th>License #</th>
                                        <th>Assessments</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($doctors) && !empty($doctors)): ?>
                                        <?php foreach ($doctors as $doctor): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($doctor['full_name']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($doctor['username']); ?></td>
                                                <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                                <td><?php echo htmlspecialchars($doctor['specialization'] ?? 'N/A'); ?></td>
                                                <td><?php echo htmlspecialchars($doctor['license_number'] ?? 'N/A'); ?></td>
                                                <td>
                                                    <span class="badge badge-info">
                                                        <?php echo $doctor['assessment_count'] ?? 0; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-<?php echo $doctor['is_active'] ? 'success' : 'danger'; ?>">
                                                        <?php echo $doctor['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="#" class="link-action" onclick="editUser(<?php echo $doctor['id']; ?>); return false;">Edit</a> |
                                                    <a href="#" class="link-action" onclick="toggleUserStatus(<?php echo $doctor['id']; ?>, '<?php echo htmlspecialchars($doctor['full_name']); ?>'); return false;">
                                                        <?php echo $doctor['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                    </a> |
                                                    <a href="#" class="link-action" style="color: #d9534f;" onclick="deleteUser(<?php echo $doctor['id']; ?>, '<?php echo htmlspecialchars($doctor['full_name']); ?>'); return false;">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" style="text-align: center; padding: 20px; color: #999;">
                                                No doctors found in the system.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Admins Table -->
                <div id="admins" class="tab-content">
                    <div class="panel panel-primary">
                        <div class="panel-header">
                            <h3>System Administrators</h3>
                            <p class="panel-description">System administrators with full access</p>
                        </div>
                        <div class="panel-body">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Created</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($admins) && !empty($admins)): ?>
                                        <?php foreach ($admins as $admin): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($admin['full_name']); ?></strong></td>
                                                <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($admin['created_at'])); ?></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $admin['is_active'] ? 'success' : 'danger'; ?>">
                                                        <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="#" class="link-action" onclick="editUser(<?php echo $admin['id']; ?>); return false;">Edit</a>
                                                    <?php if ($admin['id'] != $current_user['id']): ?>
                                                        |
                                                        <a href="#" class="link-action" onclick="toggleUserStatus(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['full_name']); ?>'); return false;">
                                                            <?php echo $admin['is_active'] ? 'Deactivate' : 'Activate'; ?>
                                                        </a> |
                                                        <a href="#" class="link-action" style="color: #d9534f;" onclick="deleteUser(<?php echo $admin['id']; ?>, '<?php echo htmlspecialchars($admin['full_name']); ?>'); return false;">Delete</a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
                                                No other administrators found in the system.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="panel panel-info">
                    <div class="panel-header">
                        <h3>User Management Guide</h3>
                    </div>
                    <div class="panel-body">
                        <ul style="list-style-position: inside; line-height: 1.8;">
                            <li><strong>Add Doctor/Admin:</strong> Click the respective buttons to register new users</li>
                            <li><strong>Edit:</strong> Modify user information including name, email, specialization, and license number</li>
                            <li><strong>Deactivate/Activate:</strong> Control system access without deleting user accounts</li>
                            <li><strong>Delete:</strong> Permanently remove user accounts from the system</li>
                            <li><strong>View Assessments:</strong> Check the assessment count for doctors to see their workload</li>
                        </ul>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <!-- Add/Edit User Modal -->
    <div id="userModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('userModal')">&times;</span>
            <h2 id="modalTitle">Add New User</h2>
            
            <div id="modalAlert" class="alert"></div>

            <form id="userForm" onsubmit="submitUserForm(event)">
                <input type="hidden" id="action" name="action" value="add">
                <input type="hidden" id="user_id" name="user_id" value="">

                <div class="form-group">
                    <label for="full_name">Full Name <span class="required">*</span></label>
                    <input type="text" id="full_name" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" id="username" name="username" required>
                    <small id="usernameNote" style="color: #999;"></small>
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password <span class="required" id="passwordRequired">*</span></label>
                    <input type="password" id="password" name="password" required>
                    <small id="passwordNote" style="color: #999;"></small>
                </div>

                <div class="form-group">
                    <label for="role">Role <span class="required">*</span></label>
                    <select id="role" name="role" required onchange="updateRoleInfo()">
                        <option value="">-- Select Role --</option>
                        <option value="doctor">Doctor</option>
                        <option value="admin">System Administrator</option>
                    </select>
                </div>

                <div id="doctorFields" style="display: none;">
                    <div class="form-group">
                        <label for="specialization">Specialization</label>
                        <input type="text" id="specialization" name="specialization" placeholder="e.g., Oncology, General Practice">
                    </div>

                    <div class="form-group">
                        <label for="license_number">Medical License Number</label>
                        <input type="text" id="license_number" name="license_number" placeholder="e.g., LIC-2024-001">
                    </div>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">Save User</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('userModal')">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            // Hide all tabs
            document.getElementById('doctors').classList.remove('active');
            document.getElementById('admins').classList.remove('active');
            
            // Remove active from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            
            // Show selected tab and mark button as active
            document.getElementById(tab).classList.add('active');
            event.target.classList.add('active');
        }

        function openAddUserModal(role) {
            // Reset form
            document.getElementById('userForm').reset();
            document.getElementById('action').value = 'add';
            document.getElementById('user_id').value = '';
            document.getElementById('modalTitle').innerText = 'Add New ' + (role === 'admin' ? 'Administrator' : 'Doctor');
            document.getElementById('role').value = role;
            document.getElementById('password').required = true;
            document.getElementById('passwordRequired').style.display = 'inline';
            document.getElementById('passwordNote').innerText = '(Minimum 6 characters)';
            document.getElementById('usernameNote').innerText = '(Cannot be changed after creation)';
            
            document.getElementById('username').disabled = false;
            document.getElementById('role').disabled = false;
            
            updateRoleInfo();
            clearAlert();
            document.getElementById('userModal').style.display = 'block';
        }

        function editUser(userId) {
            // Load user data
            fetch('/CANCER/index.php?page=api-user-action', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=get-user&user_id=' + userId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const user = data.user;
                    document.getElementById('userForm').reset();
                    document.getElementById('action').value = 'edit';
                    document.getElementById('user_id').value = user.id;
                    document.getElementById('full_name').value = user.full_name;
                    document.getElementById('username').value = user.username;
                    document.getElementById('username').disabled = true;
                    document.getElementById('email').value = user.email;
                    document.getElementById('password').value = '';
                    document.getElementById('password').required = false;
                    document.getElementById('passwordRequired').style.display = 'none';
                    document.getElementById('passwordNote').innerText = '(Leave empty to keep current password)';
                    document.getElementById('usernameNote').innerText = '';
                    document.getElementById('role').value = user.role;
                    document.getElementById('role').disabled = true;
                    document.getElementById('specialization').value = user.specialization || '';
                    document.getElementById('license_number').value = user.license_number || '';
                    
                    document.getElementById('modalTitle').innerText = 'Edit ' + user.full_name;
                    updateRoleInfo();
                    clearAlert();
                    document.getElementById('userModal').style.display = 'block';
                } else {
                    alert('Error loading user: ' + data.message);
                }
            });
        }

        function updateRoleInfo() {
            const role = document.getElementById('role').value;
            const doctorFields = document.getElementById('doctorFields');
            
            if (role === 'doctor') {
                doctorFields.style.display = 'block';
            } else {
                doctorFields.style.display = 'none';
            }
        }

        function submitUserForm(event) {
            event.preventDefault();
            
            const action = document.getElementById('action').value;
            const formData = new FormData(document.getElementById('userForm'));
            
            // Validate
            if (!document.getElementById('full_name').value || 
                !document.getElementById('email').value || 
                !document.getElementById('role').value) {
                showAlert('All required fields must be filled', 'error');
                return;
            }

            if (action === 'add' && !document.getElementById('password').value) {
                showAlert('Password is required for new users', 'error');
                return;
            }

            if (document.getElementById('password').value && document.getElementById('password').value.length < 6) {
                showAlert('Password must be at least 6 characters', 'error');
                return;
            }

            // Submit form
            fetch('/CANCER/index.php?page=api-user-action', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert(data.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                showAlert('Error: ' + error, 'error');
            });
        }

        function toggleUserStatus(userId, userName) {
            if (confirm('Are you sure you want to change the status of ' + userName + '?')) {
                fetch('/CANCER/index.php?page=api-user-action', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=toggle-status&user_id=' + userId
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        function deleteUser(userId, userName) {
            if (confirm('Are you sure you want to DELETE ' + userName + '? This action cannot be undone.')) {
                if (confirm('This will permanently remove all user data. Are you absolutely sure?')) {
                    fetch('/CANCER/index.php?page=api-user-action', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'action=delete&user_id=' + userId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    });
                }
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function showAlert(message, type) {
            const alertBox = document.getElementById('modalAlert');
            alertBox.innerHTML = message;
            alertBox.className = 'alert alert-' + type + ' show';
        }

        function clearAlert() {
            document.getElementById('modalAlert').className = 'alert';
            document.getElementById('modalAlert').innerHTML = '';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('userModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
