/**
 * ============================================================================
 * DASHBOARD UTILITIES
 * Doctor dashboard-specific JavaScript functions
 * ============================================================================
 */

/**
 * Show/hide weight loss details when checkbox is clicked
 */
document.addEventListener('DOMContentLoaded', function() {
    const weightLossCheckbox = document.querySelector('input[name="weight_loss"]');
    if (weightLossCheckbox) {
        weightLossCheckbox.addEventListener('change', function() {
            const detailsDiv = document.getElementById('weight_loss_details');
            if (detailsDiv) {
                detailsDiv.style.display = this.checked ? 'block' : 'none';
            }
        });
    }
    
    // Initialize tooltips/help text
    setupHelpTooltips();
    
    // Setup assessment form handler
    const assessmentForm = document.getElementById('quickAssessmentForm');
    if (assessmentForm) {
        assessmentForm.addEventListener('submit', handleAssessmentSubmit);
    }
});

/**
 * Handle assessment form submission
 */
function handleAssessmentSubmit(e) {
    e.preventDefault();
    
    // Validate form
    if (!validateAssessmentForm(this)) {
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[name="action"][value="assess"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Calculating Risk Assessment...';
    }
    
    // In Phase 3, this will submit to backend and receive risk results
    // For now, submit the form normally
    this.submit();
}

/**
 * Setup help tooltips for medical terms
 */
function setupHelpTooltips() {
    // Add tooltip functionality for medical terminology
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip-popup';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.position = 'fixed';
            tooltip.style.top = (rect.top - 40) + 'px';
            tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
            
            element.addEventListener('mouseleave', function() {
                tooltip.remove();
            });
        });
    });
}

/**
 * Update statistics dynamically
 * In Phase 3, this will fetch live data from backend
 */
function updateDashboardStats() {
    // TODO: Phase 3 - Implement AJAX call to fetch updated statistics
    // This function will be called periodically or on demand
}

/**
 * Export assessment data
 * Future enhancement for data export functionality
 */
function exportAssessmentData(format = 'pdf') {
    if (format === 'pdf') {
        window.print();
    } else if (format === 'csv') {
        // TODO: Implement CSV export
        alert('CSV export coming soon');
    } else if (format === 'json') {
        // TODO: Implement JSON export
        alert('JSON export coming soon');
    }
}

/**
 * Add CSS for tooltips
 */
const tooltipStyle = document.createElement('style');
tooltipStyle.textContent = `
    .tooltip-popup {
        background-color: #212121;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        max-width: 250px;
        z-index: 1000;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        word-wrap: break-word;
    }
    
    .tooltip-popup::before {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        border: 5px solid #212121 transparent transparent;
    }
`;
document.head.appendChild(tooltipStyle);
