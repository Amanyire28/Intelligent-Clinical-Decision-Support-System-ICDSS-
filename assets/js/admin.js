/**
 * ============================================================================
 * ADMIN DASHBOARD UTILITIES
 * Admin-specific JavaScript functions
 * ============================================================================
 */

document.addEventListener('DOMContentLoaded', function() {
    // Setup real-time statistics updates (future feature)
    initializeAdminDashboard();
    
    // Setup filters and sorting
    setupTableFilters();
});

/**
 * Initialize admin dashboard features
 */
function initializeAdminDashboard() {
    // TODO: Phase 4 - Implement real-time data refresh
    // This could include WebSocket updates for high-risk patient alerts
    
    console.log('Admin Dashboard initialized');
}

/**
 * Setup table filtering and sorting
 */
function setupTableFilters() {
    // Add filter functionality to tables
    const filterInputs = document.querySelectorAll('[data-filter]');
    
    filterInputs.forEach(input => {
        input.addEventListener('input', function() {
            filterTable(this);
        });
    });
}

/**
 * Filter table rows based on search input
 */
function filterTable(filterInput) {
    const filterValue = filterInput.value.toLowerCase();
    const table = filterInput.closest('table') || document.querySelector('.data-table');
    
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filterValue) ? '' : 'none';
    });
}

/**
 * Sort table by column
 */
function sortTableByColumn(columnIndex, direction = 'asc') {
    const table = document.querySelector('.data-table');
    if (!table) return;
    
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.cells[columnIndex].textContent.trim();
        const bValue = b.cells[columnIndex].textContent.trim();
        
        // Try numeric comparison first
        const aNum = parseFloat(aValue);
        const bNum = parseFloat(bValue);
        
        if (!isNaN(aNum) && !isNaN(bNum)) {
            return direction === 'asc' ? aNum - bNum : bNum - aNum;
        }
        
        // Fall back to string comparison
        return direction === 'asc' 
            ? aValue.localeCompare(bValue)
            : bValue.localeCompare(aValue);
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

/**
 * Format risk statistics display
 */
function updateRiskDistribution(stats) {
    // TODO: Phase 4 - Implement visualization of risk distribution
    // Could use a chart library for better visualization
    
    console.log('Risk Distribution:', stats);
}

/**
 * Alert high-risk patients
 * Future feature for real-time alerts
 */
function checkHighRiskPatients() {
    // TODO: Phase 4 - Implement alerts for new high-risk cases
    // Could use browser notifications or toast messages
}

/**
 * Export admin report
 */
function exportAdminReport(format = 'pdf') {
    const reportName = 'ICDSS_Admin_Report_' + new Date().toISOString().split('T')[0];
    
    if (format === 'pdf') {
        window.print();
    } else if (format === 'csv') {
        // TODO: Implement CSV export for admin reports
        alert('CSV export coming soon');
    } else if (format === 'excel') {
        // TODO: Implement Excel export for admin reports
        alert('Excel export coming soon');
    }
}

/**
 * Refresh dashboard data
 * Will be called periodically to update statistics
 */
function refreshAdminDashboard() {
    // TODO: Phase 3 - Implement AJAX call to refresh statistics
    // This will update all stat cards and tables with latest data
    
    console.log('Dashboard refresh triggered at', new Date().toLocaleTimeString());
}

/**
 * Monitor system health
 */
function monitorSystemHealth() {
    // TODO: Phase 4 - Implement system health monitoring
    // Check database connection, API response times, etc.
}
