// Sandy Beauty Nails - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add loading state to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Procesando...';
                
                // Re-enable after 30 seconds as fallback
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 30000);
            }
        });
    });

    // Format phone numbers
    const phoneInputs = document.querySelectorAll('input[type="tel"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length >= 7) {
                value = value.replace(/(\d{3})(\d{4})/, '$1-$2');
            }
            e.target.value = value;
        });
    });

    // Initialize Charts if Chart.js is available
    if (typeof Chart !== 'undefined') {
        initializeCharts();
    }
});

// Utility functions
const Utils = {
    // Show loading modal
    showLoading: function() {
        const modal = document.getElementById('loadingModal');
        if (modal) {
            try {
                if (typeof bootstrap !== 'undefined') {
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                } else {
                    // Fallback without Bootstrap
                    modal.style.display = 'block';
                    modal.classList.add('show');
                    if (!document.querySelector('.modal-backdrop')) {
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        backdrop.style.cssText = 'position: fixed; top: 0; left: 0; z-index: 1040; width: 100vw; height: 100vh; background-color: #000; opacity: 0.5;';
                        document.body.appendChild(backdrop);
                    }
                }
            } catch (e) {
                console.error('Error showing loading modal:', e);
            }
        }
    },

    // Hide loading modal
    hideLoading: function() {
        const modal = document.getElementById('loadingModal');
        if (modal) {
            try {
                if (typeof bootstrap !== 'undefined') {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) {
                        bsModal.hide();
                    }
                } else {
                    // Fallback without Bootstrap
                    modal.style.display = 'none';
                    modal.classList.remove('show');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            } catch (e) {
                console.error('Error hiding loading modal:', e);
                // Force hide
                modal.style.display = 'none';
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }
        }
    },

    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('es-MX', {
            style: 'currency',
            currency: 'MXN'
        }).format(amount);
    },

    // Format date
    formatDate: function(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-MX', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    },

    // Format time
    formatTime: function(timeString) {
        const [hours, minutes] = timeString.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes));
        return date.toLocaleTimeString('es-MX', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    },

    // AJAX helper
    ajax: function(url, options = {}) {
        const defaults = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        const config = Object.assign(defaults, options);

        return fetch(url, config)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .catch(error => {
                console.error('AJAX Error:', error);
                throw error;
            });
    },

    // Show toast notification
    showToast: function(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || this.createToastContainer();
        
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        
        try {
            if (typeof bootstrap !== 'undefined') {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.show();
            } else {
                // Fallback: show as alert
                alert(message);
                toast.remove();
                return;
            }
        } catch (e) {
            // Fallback: show as alert
            alert(message);
            toast.remove();
            return;
        }

        // Remove toast element after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
        
        // Auto-remove after 5 seconds if Bootstrap events don't work
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    },

    // Create toast container if it doesn't exist
    createToastContainer: function() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        container.style.zIndex = '1050';
        document.body.appendChild(container);
        return container;
    }
};

// Initialize charts
function initializeCharts() {
    // Revenue Chart
    const revenueChart = document.getElementById('revenueChart');
    if (revenueChart) {
        const ctx = revenueChart.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Ingresos',
                    data: [],
                    borderColor: 'rgb(233, 30, 99)',
                    backgroundColor: 'rgba(233, 30, 99, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return Utils.formatCurrency(value);
                            }
                        }
                    }
                }
            }
        });
    }

    // Services Chart
    const servicesChart = document.getElementById('servicesChart');
    if (servicesChart) {
        const ctx = servicesChart.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#e91e63',
                        '#9c27b0',
                        '#673ab7',
                        '#3f51b5',
                        '#2196f3',
                        '#00bcd4',
                        '#009688',
                        '#4caf50'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Admin utilities
const Admin = {
    // Update appointment status
    updateStatus: function(appointmentId, status, csrfToken) {
        Utils.showLoading();
        
        const formData = new FormData();
        formData.append('appointment_id', appointmentId);
        formData.append('status', status);
        formData.append('csrf_token', csrfToken);

        fetch(window.location.origin + '/admin/update-status', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            Utils.hideLoading();
            if (data.success) {
                Utils.showToast(data.message, 'success');
                // Reload page to update status
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                Utils.showToast(data.error || 'Error al actualizar', 'danger');
            }
        })
        .catch(error => {
            Utils.hideLoading();
            Utils.showToast('Error de conexión', 'danger');
            console.error('Error:', error);
        });
    },

    // Filter appointments
    filterAppointments: function() {
        const form = document.getElementById('filterForm');
        if (form) {
            form.submit();
        }
    },

    // Export data
    exportData: function(type, format = 'csv') {
        Utils.showToast('Función de exportación en desarrollo', 'info');
    }
};

// Global utilities
window.Utils = Utils;
window.Admin = Admin;