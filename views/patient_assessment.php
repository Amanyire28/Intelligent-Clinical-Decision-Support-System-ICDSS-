<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Assessment - ICDSS</title>
    <link rel="stylesheet" href="/CANCER/assets/css/style.css">
    <style>
        .patient-selection-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            gap: 40px;
        }
        
        .selection-buttons {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .selection-card {
            background: white;
            border: 2px solid #ddd;
            border-radius: 8px;
            padding: 40px;
            min-width: 250px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .selection-card:hover {
            border-color: #007bff;
            box-shadow: 0 5px 15px rgba(0,123,255,0.2);
            transform: translateY(-2px);
        }
        
        .selection-card-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .selection-card h3 {
            margin: 15px 0;
            color: #333;
        }
        
        .selection-card p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
        }
        
        .selection-card button {
            margin-top: 20px;
            width: 100%;
        }
        
        .patient-search-screen {
            display: none;
        }
        
        .search-input-container {
            margin-bottom: 20px;
        }
        
        .search-results {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            background: #f9f9f9;
        }
        
        .search-result-item {
            padding: 15px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .search-result-item:hover {
            background: #f0f0f0;
        }
        
        .result-patient-info {
            flex: 1;
        }
        
        .result-patient-info h4 {
            margin: 0;
            color: #333;
        }
        
        .result-patient-info p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        
        .result-select-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .result-select-btn:hover {
            background: #0056b3;
        }
        
        .patient-history-panel {
            display: none;
            margin-bottom: 30px;
        }
        
        .patient-header {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .patient-details h3 {
            margin: 0;
            color: #333;
        }
        
        .patient-details p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }
        
        .patient-actions button {
            margin-right: 10px;
        }
        
        .assessment-history {
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .assessment-item {
            padding: 15px;
            border-left: 4px solid #007bff;
            background: #f9f9f9;
            margin-bottom: 10px;
            border-radius: 3px;
        }
        
        .assessment-item.high-risk {
            border-left-color: #d32f2f;
        }
        
        .assessment-item.moderate-risk {
            border-left-color: #ff9800;
        }
        
        .assessment-item h5 {
            margin: 0 0 8px 0;
            color: #333;
        }
        
        .assessment-meta {
            display: flex;
            gap: 20px;
            font-size: 12px;
            color: #666;
            margin-bottom: 8px;
        }
        
        .assessment-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .badge-small {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .badge-small.low {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-small.moderate {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-small.high {
            background: #f8d7da;
            color: #721c24;
        }
        
        .no-history-message {
            text-align: center;
            padding: 20px;
            color: #666;
            font-style: italic;
        }
        
        .back-to-selection {
            margin-bottom: 20px;
        }
        
        .back-to-selection button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .back-to-selection button:hover {
            background: #5a6268;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Navigation Header -->
        <nav class="navbar navbar-main">
            <div class="navbar-brand">
                <h1 class="system-title">
                    <span class="title-icon">⚕️</span> ICDSS - Patient Assessment
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
                        <li><a href="/CANCER/index.php?page=patient-assessment" class="nav-link active">New Assessment</a></li>
                        <li><a href="/CANCER/index.php?page=patient-search" class="nav-link">Patient History</a></li>
                    </ul>
                </div>
            </aside>

            <!-- Main Content Panel -->
            <section class="content-panel">
                <!-- STAGE 1: PATIENT SELECTION -->
                <div id="patientSelectionStage" class="patient-selection-screen">
                    <div class="dashboard-header" style="text-align: center;">
                        <h2>Start New Assessment</h2>
                        <p class="subtitle">Is this patient new or returning for follow-up?</p>
                    </div>

                    <div class="selection-buttons">
                        <!-- New Patient Option -->
                        <div class="selection-card">
                            <div class="selection-card-icon">👤</div>
                            <h3>New Patient</h3>
                            <p>Patient visiting for the first time. Create new patient record and begin assessment.</p>
                            <button type="button" class="btn btn-primary" onclick="selectNewPatient()">
                                New Patient
                            </button>
                        </div>

                        <!-- Returning Patient Option -->
                        <div class="selection-card">
                            <div class="selection-card-icon">📋</div>
                            <h3>Returning Patient</h3>
                            <p>Patient has previous assessments. Review history and create follow-up assessment.</p>
                            <button type="button" class="btn btn-primary" onclick="selectReturningPatient()">
                                Returning Patient
                            </button>
                        </div>
                    </div>
                </div>

                <!-- STAGE 2A: RETURNING PATIENT SEARCH -->
                <div id="patientSearchStage" class="patient-search-screen">
                    <div class="dashboard-header">
                        <h2>Find Returning Patient</h2>
                        <p class="subtitle">Search by patient name or medical record number</p>
                    </div>

                    <div class="panel panel-primary">
                        <div class="panel-body">
                            <div class="search-input-container">
                                <label for="patientSearchInput" class="form-label">Search Patient:</label>
                                <input 
                                    type="text" 
                                    id="patientSearchInput" 
                                    class="form-control" 
                                    placeholder="Enter patient name or MRN..."
                                    autocomplete="off"
                                >
                            </div>

                            <div id="searchResultsContainer" class="search-results" style="display: none;">
                                <div id="searchResultsList"></div>
                            </div>

                            <div id="noSearchResults" class="no-history-message" style="display: none;">
                                No patients found. Try a different search term.
                            </div>

                            <div id="searchLoadingSpinner" style="display: none; text-align: center;">
                                <p style="color: #666;">Searching...</p>
                            </div>
                        </div>
                    </div>

                    <div class="back-to-selection" style="margin-top: 20px;">
                        <button type="button" onclick="backToSelection()">← Back to Selection</button>
                    </div>
                </div>

                <!-- STAGE 2B: NEW PATIENT FORM OR RETURNING PATIENT HISTORY + FORM -->
                <div id="assessmentFormStage" style="display: none;">
                    <!-- Patient History Panel (for returning patients) -->
                    <div id="patientHistoryPanel" class="patient-history-panel">
                        <div class="patient-header">
                            <div class="patient-details">
                                <h3 id="patientDisplayName"></h3>
                                <p id="patientDisplayInfo"></p>
                            </div>
                            <div class="patient-actions">
                                <button type="button" class="btn btn-secondary btn-small" onclick="backToPatientSearch()">
                                    Change Patient
                                </button>
                            </div>
                        </div>

                        <h4 style="margin-top: 20px;">Previous Assessments</h4>
                        <div id="previousAssessmentsContainer"></div>
                    </div>

                    <!-- Assessment Form -->
                    <div class="panel panel-primary">
                        <div class="panel-header">
                            <h3 id="formTitle">Patient Risk Assessment</h3>
                            <p class="panel-description" id="formSubtitle">Complete all sections for accurate risk scoring</p>
                        </div>
                        <div class="panel-body">
                            <form id="quickAssessmentForm" action="/CANCER/submit_assessment.php" method="POST">
                                <!-- Hidden patient_id field for returning patients -->
                                <input type="hidden" id="patient_id" name="patient_id" value="">
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
                                    <input type="checkbox" name="weight_loss" value="1" id="weight_loss">
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
                                    <input type="checkbox" name="family_cancer_history" value="1" id="family_cancer_history">
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

                                <!-- Submit Button -->
                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary">Submit Assessment</button>
                                    <a href="/CANCER/index.php?page=doctor-dashboard" class="btn btn-secondary">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script>
        let selectedPatientId = null;
        let isNewPatient = true;

        // Patient Selection
        function selectNewPatient() {
            isNewPatient = true;
            selectedPatientId = null;
            document.getElementById('patientSelectionStage').style.display = 'none';
            document.getElementById('assessmentFormStage').style.display = 'block';
            document.getElementById('patientHistoryPanel').style.display = 'none';
            document.getElementById('formTitle').textContent = 'New Patient Assessment';
            document.getElementById('formSubtitle').textContent = 'Create new patient record and complete assessment';
            document.getElementById('patient_id').value = '';
            
            // Enable patient fields
            document.getElementById('first_name').disabled = false;
            document.getElementById('last_name').disabled = false;
            document.getElementById('date_of_birth').disabled = false;
            document.getElementById('gender').disabled = false;
            
            window.scrollTo(0, 0);
        }

        function selectReturningPatient() {
            isNewPatient = false;
            document.getElementById('patientSelectionStage').style.display = 'none';
            document.getElementById('patientSearchStage').style.display = 'block';
            document.getElementById('assessmentFormStage').style.display = 'none';
            
            // Focus on search
            setTimeout(() => {
                document.getElementById('patientSearchInput').focus();
            }, 100);
            
            window.scrollTo(0, 0);
        }

        function backToSelection() {
            document.getElementById('patientSearchStage').style.display = 'none';
            document.getElementById('assessmentFormStage').style.display = 'none';
            document.getElementById('patientSelectionStage').style.display = 'flex';
            document.getElementById('patientSearchInput').value = '';
            window.scrollTo(0, 0);
        }

        function backToPatientSearch() {
            document.getElementById('assessmentFormStage').style.display = 'none';
            document.getElementById('patientSearchStage').style.display = 'block';
            document.getElementById('patientSearchInput').focus();
            window.scrollTo(0, 0);
        }

        // Patient Search
        document.getElementById('patientSearchInput')?.addEventListener('input', async function() {
            const query = this.value.trim();
            console.log('Search input changed: "' + query + '" (length: ' + query.length + ')');
            
            if (query.length < 2) {
                console.log('Query too short, hiding results');
                document.getElementById('searchResultsContainer').style.display = 'none';
                document.getElementById('noSearchResults').style.display = 'none';
                return;
            }

            console.log('Starting search...');
            document.getElementById('searchLoadingSpinner').style.display = 'block';
            document.getElementById('searchResultsContainer').style.display = 'none';
            document.getElementById('noSearchResults').style.display = 'none';

            try {
                console.log('Fetching: /CANCER/index.php?page=api-patient-search');
                const response = await fetch('/CANCER/index.php?page=api-patient-search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'search_term=' + encodeURIComponent(query)
                });

                console.log('Response status: ' + response.status);
                const data = await response.json();
                console.log('Response data:', data);
                document.getElementById('searchLoadingSpinner').style.display = 'none';

                // Check if we have valid data
                if (data && data.success === true && Array.isArray(data.patients) && data.patients.length > 0) {
                    console.log('Found ' + data.patients.length + ' patients, displaying results');
                    displaySearchResults(data.patients);
                } else {
                    console.log('No patients found or invalid response');
                    console.log('data.success:', data.success);
                    console.log('data.patients:', data.patients);
                    document.getElementById('searchResultsContainer').style.display = 'none';
                    document.getElementById('noSearchResults').style.display = 'block';
                }
            } catch (error) {
                document.getElementById('searchLoadingSpinner').style.display = 'none';
                console.error('Search error:', error);
                document.getElementById('noSearchResults').style.display = 'block';
            }
        });

        function displaySearchResults(patients) {
            const container = document.getElementById('searchResultsList');
            container.innerHTML = '';

            patients.forEach(patient => {
                const age = calculateAge(patient.date_of_birth);
                const lastAssessmentText = patient.last_assessment_date 
                    ? `Last assessment: ${new Date(patient.last_assessment_date).toLocaleDateString()}`
                    : 'No previous assessments';
                
                const resultDiv = document.createElement('div');
                resultDiv.className = 'search-result-item';
                
                const infoDiv = document.createElement('div');
                infoDiv.className = 'result-patient-info';
                
                const nameHeading = document.createElement('h4');
                nameHeading.textContent = `${patient.first_name} ${patient.last_name}`;
                
                const metaP = document.createElement('p');
                metaP.textContent = `MRN: ${patient.medical_record_number || 'N/A'} | Age: ${age} | ${lastAssessmentText}`;
                
                infoDiv.appendChild(nameHeading);
                infoDiv.appendChild(metaP);
                
                const selectBtn = document.createElement('button');
                selectBtn.type = 'button';
                selectBtn.className = 'result-select-btn';
                selectBtn.textContent = 'Select';
                selectBtn.addEventListener('click', function() {
                    selectPatient(patient.id, patient.first_name, patient.last_name, patient.date_of_birth, patient.gender);
                });
                
                resultDiv.appendChild(infoDiv);
                resultDiv.appendChild(selectBtn);
                
                container.appendChild(resultDiv);
            });

            document.getElementById('searchResultsContainer').style.display = 'block';
        }

        function calculateAge(dateOfBirth) {
            const birthDate = new Date(dateOfBirth);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const month = today.getMonth() - birthDate.getMonth();
            if (month < 0 || (month === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            return age;
        }

        function htmlEscape(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        async function selectPatient(patientId, firstName, lastName, dateOfBirth, gender) {
            isNewPatient = false;
            selectedPatientId = patientId;

            // Fetch patient assessments
            try {
                const response = await fetch('/CANCER/index.php?page=api-patient-assessments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'patient_id=' + patientId
                });

                const data = await response.json();

                // Populate patient info
                document.getElementById('patientDisplayName').textContent = `${firstName} ${lastName}`;
                const age = calculateAge(dateOfBirth);
                document.getElementById('patientDisplayInfo').textContent = 
                    `DOB: ${dateOfBirth} | Age: ${age} | Gender: ${gender}`;

                // Populate form fields
                document.getElementById('patient_id').value = patientId;
                document.getElementById('first_name').value = firstName;
                document.getElementById('last_name').value = lastName;
                document.getElementById('date_of_birth').value = dateOfBirth;
                document.getElementById('gender').value = gender;

                // Disable patient fields (read-only for returning patients)
                document.getElementById('first_name').disabled = true;
                document.getElementById('last_name').disabled = true;
                document.getElementById('date_of_birth').disabled = true;
                document.getElementById('gender').disabled = true;

                // Display assessment history
                if (data.success && data.assessments.length > 0) {
                    displayAssessmentHistory(data.assessments);
                } else {
                    document.getElementById('previousAssessmentsContainer').innerHTML = 
                        '<p class="no-history-message">No previous assessments found for this patient.</p>';
                }

                // Show form
                document.getElementById('patientSearchStage').style.display = 'none';
                document.getElementById('assessmentFormStage').style.display = 'block';
                document.getElementById('patientHistoryPanel').style.display = 'block';
                document.getElementById('formTitle').textContent = 'Follow-up Assessment';
                document.getElementById('formSubtitle').textContent = 'Create new assessment - all data is incremental';

                window.scrollTo(0, 0);
            } catch (error) {
                console.error('Error fetching assessments:', error);
                alert('Error loading patient history. Please try again.');
            }
        }

        function displayAssessmentHistory(assessments) {
            const container = document.getElementById('previousAssessmentsContainer');
            container.innerHTML = '';

            assessments.forEach((assessment, index) => {
                const assessmentNumber = assessments.length - index;
                const date = new Date(assessment.assessment_date);
                const riskLevel = assessment.risk_level || 'Unknown';
                const riskScore = assessment.risk_score || '?';
                const riskClass = riskLevel.toLowerCase();

                const hasOutcome = assessment.outcome_status === 'recorded';
                const outcomeText = hasOutcome ? '✓ Outcome recorded' : '⏳ Pending outcome';

                const html = `
                    <div class="assessment-item ${riskClass}-risk">
                        <h5>Assessment #${assessmentNumber} - ${date.toLocaleDateString()}</h5>
                        <div class="assessment-meta">
                            <span><strong>Risk:</strong> <span class="badge-small ${riskClass}">${riskLevel}</span></span>
                            <span><strong>Score:</strong> ${riskScore}/100</span>
                            <span><strong>Status:</strong> ${outcomeText}</span>
                        </div>
                    </div>
                `;
                container.innerHTML += html;
            });

            container.innerHTML += '<p style="margin-top: 15px; color: #666; font-size: 13px; font-style: italic;">Review above history before creating new assessment. Your new findings will be compared against this history.</p>';
        }

        // Form visibility toggles
        document.getElementById('weight_loss')?.addEventListener('change', function() {
            document.getElementById('weight_loss_details').style.display = 
                this.checked ? 'block' : 'none';
        });

        document.getElementById('family_cancer_history')?.addEventListener('change', function() {
            document.getElementById('family_cancer_details').style.display = 
                this.checked ? 'block' : 'none';
        });

        document.getElementById('smoking_status')?.addEventListener('change', function() {
            document.getElementById('smoking_years_div').style.display = 
                (this.value === 'former' || this.value === 'current') ? 'block' : 'none';
        });
    </script>
</body>
</html>
