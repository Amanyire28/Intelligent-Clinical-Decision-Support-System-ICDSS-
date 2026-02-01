/**
 * ============================================================================
 * FORM VALIDATION & UTILITIES
 * Client-side validation for medical forms
 * ============================================================================
 */

/**
 * Validate assessment form before submission
 * Ensures required fields are filled and data types are correct
 */
function validateAssessmentForm(formElement) {
    // Reset validation errors
    clearValidationErrors();
    
    let isValid = true;
    const errors = [];
    
    // Age validation
    const age = document.getElementById('age');
    if (age && age.value) {
        const ageVal = parseInt(age.value);
        if (isNaN(ageVal) || ageVal < 18 || ageVal > 120) {
            errors.push('Age must be between 18 and 120 years');
            highlightField(age);
            isValid = false;
        }
    }
    
    // Smoking years validation
    const smokingStatus = document.getElementById('smoking_status');
    const smokingYears = document.getElementById('smoking_years');
    
    if (smokingStatus && smokingStatus.value !== 'never' && smokingYears) {
        if (!smokingYears.value) {
            errors.push('Please enter smoking duration for former/current smokers');
            highlightField(smokingYears);
            isValid = false;
        } else {
            const yearsVal = parseInt(smokingYears.value);
            if (isNaN(yearsVal) || yearsVal < 0 || yearsVal > 80) {
                errors.push('Smoking duration must be between 0 and 80 years');
                highlightField(smokingYears);
                isValid = false;
            }
        }
    }
    
    // Sore throat duration validation
    const soreThroatDuration = document.getElementById('sore_throat_duration');
    if (soreThroatDuration && soreThroatDuration.value) {
        const daysVal = parseInt(soreThroatDuration.value);
        if (isNaN(daysVal) || daysVal < 0) {
            errors.push('Sore throat duration must be a positive number');
            highlightField(soreThroatDuration);
            isValid = false;
        }
    }
    
    // Weight loss validation
    const weightLoss = document.querySelector('input[name="weight_loss"]');
    if (weightLoss && weightLoss.checked) {
        const weightLossPercentage = document.getElementById('weight_loss_percentage');
        const weightLossWeeks = document.getElementById('weight_loss_weeks');
        
        if (weightLossPercentage && weightLossPercentage.value) {
            const percVal = parseFloat(weightLossPercentage.value);
            if (isNaN(percVal) || percVal < 0 || percVal > 100) {
                errors.push('Weight loss percentage must be between 0 and 100');
                highlightField(weightLossPercentage);
                isValid = false;
            }
        }
        
        if (weightLossWeeks && weightLossWeeks.value) {
            const weeksVal = parseInt(weightLossWeeks.value);
            if (isNaN(weeksVal) || weeksVal < 0) {
                errors.push('Weight loss duration must be a positive number');
                highlightField(weightLossWeeks);
                isValid = false;
            }
        }
    }
    
    // Lab indicators validation
    const hemoglobin = document.getElementById('hemoglobin_level');
    if (hemoglobin && hemoglobin.value) {
        const hemVal = parseFloat(hemoglobin.value);
        if (isNaN(hemVal) || hemVal < 5 || hemVal > 20) {
            errors.push('Hemoglobin level should be between 5 and 20 g/dL');
            highlightField(hemoglobin);
            isValid = false;
        }
    }
    
    const lymphocyte = document.getElementById('lymphocyte_count');
    if (lymphocyte && lymphocyte.value) {
        const lymphVal = parseFloat(lymphocyte.value);
        if (isNaN(lymphVal) || lymphVal < 0 || lymphVal > 10) {
            errors.push('Lymphocyte count should be between 0 and 10 K/uL');
            highlightField(lymphocyte);
            isValid = false;
        }
    }
    
    // Display errors
    if (!isValid && errors.length > 0) {
        displayValidationErrors(errors);
    }
    
    return isValid;
}

/**
 * Validate login form
 */
function validateLoginForm(formElement) {
    const username = document.getElementById('username');
    const password = document.getElementById('password');
    
    let isValid = true;
    
    if (!username || !username.value.trim()) {
        highlightField(username);
        isValid = false;
    }
    
    if (!password || !password.value) {
        highlightField(password);
        isValid = false;
    }
    
    return isValid;
}

/**
 * Highlight invalid form field
 */
function highlightField(field) {
    if (!field) return;
    field.classList.add('invalid');
    field.style.borderColor = '#F44336';
}

/**
 * Clear validation highlights
 */
function clearValidationErrors() {
    const invalidFields = document.querySelectorAll('.invalid');
    invalidFields.forEach(field => {
        field.classList.remove('invalid');
        field.style.borderColor = '';
    });
    
    const errorContainer = document.getElementById('validationErrors');
    if (errorContainer) {
        errorContainer.innerHTML = '';
        errorContainer.style.display = 'none';
    }
}

/**
 * Display validation errors
 */
function displayValidationErrors(errors) {
    let errorHtml = '<div class="alert alert-error">';
    errorHtml += '<strong>Validation Errors:</strong><ul class="error-list">';
    
    errors.forEach(error => {
        errorHtml += `<li>${error}</li>`;
    });
    
    errorHtml += '</ul></div>';
    
    // Create error container if it doesn't exist
    let errorContainer = document.getElementById('validationErrors');
    if (!errorContainer) {
        errorContainer = document.createElement('div');
        errorContainer.id = 'validationErrors';
        const form = document.querySelector('form');
        if (form) {
            form.insertBefore(errorContainer, form.firstChild);
        }
    }
    
    errorContainer.innerHTML = errorHtml;
    errorContainer.style.display = 'block';
    errorContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/**
 * Toggle visibility of conditional fields based on checkbox/select changes
 */
function setupConditionalFields() {
    // Smoking years visibility
    const smokingStatus = document.getElementById('smoking_status');
    const smokingYearsDiv = document.getElementById('smoking_years_div');
    
    if (smokingStatus && smokingYearsDiv) {
        smokingStatus.addEventListener('change', function() {
            if (this.value === 'never') {
                smokingYearsDiv.style.display = 'none';
            } else {
                smokingYearsDiv.style.display = 'block';
            }
        });
    }
    
    // Weight loss details visibility
    const weightLossCheckbox = document.querySelector('input[name="weight_loss"]');
    const weightLossDetails = document.getElementById('weight_loss_details');
    
    if (weightLossCheckbox && weightLossDetails) {
        weightLossCheckbox.addEventListener('change', function() {
            weightLossDetails.style.display = this.checked ? 'block' : 'none';
        });
    }
    
    // Family cancer history details
    const familyCancerCheckbox = document.querySelector('input[name="family_cancer_history"]');
    const familyCancerDetails = document.getElementById('family_cancer_details');
    
    if (familyCancerCheckbox && familyCancerDetails) {
        familyCancerCheckbox.addEventListener('change', function() {
            familyCancerDetails.style.display = this.checked ? 'block' : 'none';
        });
    }
}

/**
 * Patient search with autocomplete
 */
function setupPatientSearch() {
    const searchInput = document.getElementById('patient_search');
    const suggestionsDiv = document.getElementById('patientSuggestions');
    
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.trim();
        
        if (searchTerm.length < 2) {
            if (suggestionsDiv) suggestionsDiv.innerHTML = '';
            return;
        }
        
        // Mock patient search (in real implementation, this would call backend API)
        searchPatients(searchTerm);
    });
}

/**
 * Mock patient search function
 * In Phase 3, this will be replaced with actual AJAX call to backend
 */
function searchPatients(searchTerm) {
    // TODO: Phase 3 - Implement AJAX call to PatientController
    // Example: fetch('/CANCER/controllers/PatientController.php?action=search&term=' + encodeURIComponent(searchTerm))
    
    const suggestionsDiv = document.getElementById('patientSuggestions');
    if (!suggestionsDiv) return;
    
    // Mock data for demonstration
    const mockPatients = [
        { id: 1, name: 'John Smith', mrn: 'MRN001' },
        { id: 2, name: 'Jane Johnson', mrn: 'MRN002' },
        { id: 3, name: 'Robert Williams', mrn: 'MRN003' },
    ];
    
    const matches = mockPatients.filter(p => 
        p.name.toLowerCase().includes(searchTerm.toLowerCase()) ||
        p.mrn.toLowerCase().includes(searchTerm.toLowerCase())
    );
    
    if (matches.length === 0) {
        suggestionsDiv.innerHTML = '<div class="suggestion-item">No patients found</div>';
        return;
    }
    
    suggestionsDiv.innerHTML = matches.map(patient => 
        `<div class="suggestion-item" onclick="selectPatient(${patient.id}, '${patient.name}')">${patient.name} (${patient.mrn})</div>`
    ).join('');
}

/**
 * Select patient from autocomplete
 */
function selectPatient(patientId, patientName) {
    const searchInput = document.getElementById('patient_search');
    const suggestionsDiv = document.getElementById('patientSuggestions');
    
    if (searchInput) {
        searchInput.value = patientName;
        searchInput.dataset.patientId = patientId;
    }
    
    if (suggestionsDiv) {
        suggestionsDiv.innerHTML = '';
    }
}

/**
 * Format numbers for display
 */
function formatNumber(num, decimals = 1) {
    if (isNaN(num)) return 'N/A';
    return parseFloat(num).toFixed(decimals);
}

/**
 * Calculate patient age from date
 */
function calculateAge(birthDate) {
    const today = new Date();
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

/**
 * Debounce function for search inputs
 */
function debounce(func, delay) {
    let timeoutId;
    return function(...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => func.apply(this, args), delay);
    };
}

/**
 * Initialize form event listeners
 */
document.addEventListener('DOMContentLoaded', function() {
    // Setup conditional fields
    setupConditionalFields();
    
    // Setup patient search
    setupPatientSearch();
    
    // Form submission validation
    const assessmentForm = document.getElementById('quickAssessmentForm');
    if (assessmentForm) {
        assessmentForm.addEventListener('submit', function(e) {
            if (!validateAssessmentForm(this)) {
                e.preventDefault();
            }
        });
    }
    
    // Clear validation errors on input
    const formControls = document.querySelectorAll('.form-control');
    formControls.forEach(control => {
        control.addEventListener('focus', function() {
            this.classList.remove('invalid');
            this.style.borderColor = '';
        });
    });
    
    // Add CSS for invalid fields
    const style = document.createElement('style');
    style.textContent = `
        .form-control.invalid {
            border-color: #F44336 !important;
            background-color: rgba(244, 67, 54, 0.05) !important;
        }
        
        .error-list {
            list-style-position: inside;
            margin-top: 8px;
        }
        
        .error-list li {
            padding: 4px 0;
            color: inherit;
        }
    `;
    document.head.appendChild(style);
});
