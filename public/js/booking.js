// Booking Form JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    const steps = document.querySelectorAll('.booking-step');
    let currentStep = 1;
    let customerData = null;
    let serviceData = null;

    // Initialize booking form
    initializeBookingForm();

    function initializeBookingForm() {
        // Step 1: Customer verification
        const phoneInput = document.getElementById('phone');
        const checkCustomerBtn = document.getElementById('checkCustomerBtn');
        const nextStep1Btn = document.getElementById('nextStep1');

        phoneInput.addEventListener('input', function() {
            const phone = this.value.trim();
            if (phone.length >= 7) {
                checkCustomerBtn.disabled = false;
            } else {
                checkCustomerBtn.disabled = true;
                nextStep1Btn.disabled = true;
            }
        });

        checkCustomerBtn.addEventListener('click', function() {
            checkCustomer();
        });

        // Step 2: Service selection
        const serviceSelect = document.getElementById('service_id');
        const nextStep2Btn = document.getElementById('nextStep2');

        serviceSelect.addEventListener('change', function() {
            updateServiceInfo();
            nextStep2Btn.disabled = !this.value;
        });

        // Step 3: Date and time selection
        const dateInput = document.getElementById('appointment_date');
        const timeSelect = document.getElementById('appointment_time');
        const nextStep3Btn = document.getElementById('nextStep3');

        dateInput.addEventListener('change', function() {
            if (this.value) {
                loadAvailableSlots();
            }
        });

        timeSelect.addEventListener('change', function() {
            nextStep3Btn.disabled = !this.value;
        });

        // Navigation buttons
        setupNavigationButtons();

        // Auto-advance if data is pre-filled (from session)
        if (phoneInput.value) {
            checkCustomer();
        }
    }

    function checkCustomer() {
        const phone = document.getElementById('phone').value.trim();
        const customerInfoDiv = document.getElementById('customerInfo');
        const nextBtn = document.getElementById('nextStep1');

        if (!phone) {
            Utils.showToast('Por favor ingresa un número de teléfono', 'warning');
            return;
        }

        // Basic phone validation
        if (phone.length < 7) {
            Utils.showToast('El número de teléfono debe tener al menos 7 dígitos', 'warning');
            return;
        }

        Utils.showLoading();

        const formData = new FormData();
        formData.append('phone', phone);

        // Construct URL more robustly to handle different base configurations
        let baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
        let checkCustomerUrl = baseUrl + '/booking/check-customer';
        
        // Clean up double slashes except after protocol
        checkCustomerUrl = checkCustomerUrl.replace(/([^:]\/)\/+/g, '$1');

        fetch(checkCustomerUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            Utils.hideLoading();
            
            if (data.error) {
                Utils.showToast(data.error, 'danger');
                return;
            }
            
            customerInfoDiv.style.display = 'block';
            
            if (data.exists) {
                // Pre-fill customer data
                document.getElementById('name').value = data.customer.name || '';
                document.getElementById('email').value = data.customer.email || '';
                document.getElementById('cedula').value = data.customer.cedula || '';
                
                customerData = data.customer;
                
                Utils.showToast(`¡Hola ${data.customer.name}! Datos cargados correctamente`, 'success');
                
                // Show customer info
                if (data.customer.total_appointments > 0) {
                    const infoHtml = `
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-user-check me-2"></i>
                            <strong>Cliente frecuente:</strong> ${data.customer.total_appointments} citas anteriores
                        </div>
                    `;
                    customerInfoDiv.insertAdjacentHTML('beforeend', infoHtml);
                }
            } else {
                // Clear fields for new customer
                document.getElementById('name').value = '';
                document.getElementById('email').value = '';
                document.getElementById('cedula').value = '';
                
                Utils.showToast(data.message || 'Cliente nuevo. Completa tus datos para continuar', 'info');
            }
            
            // Enable next button
            nextBtn.disabled = false;
            
            // Focus on name field
            document.getElementById('name').focus();
        })
        .catch(error => {
            Utils.hideLoading();
            Utils.showToast('Error al verificar cliente. Intente nuevamente.', 'danger');
            console.error('Error:', error);
            
            // Still show customer info section for new customer entry
            customerInfoDiv.style.display = 'block';
            nextBtn.disabled = false;
        });
    }

    function updateServiceInfo() {
        const serviceSelect = document.getElementById('service_id');
        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const descriptionDiv = document.getElementById('serviceDescription');

        if (selectedOption.value) {
            const price = selectedOption.getAttribute('data-price');
            const duration = selectedOption.getAttribute('data-duration');
            
            serviceData = {
                id: selectedOption.value,
                name: selectedOption.text.split(' - ')[0],
                price: parseFloat(price),
                duration: parseInt(duration)
            };

            document.getElementById('servicePrice').textContent = Utils.formatCurrency(price);
            document.getElementById('serviceDuration').textContent = duration;
            
            descriptionDiv.style.display = 'block';
        } else {
            descriptionDiv.style.display = 'none';
            serviceData = null;
        }
    }

    function loadAvailableSlots() {
        const date = document.getElementById('appointment_date').value;
        const manicuristId = document.getElementById('manicurist_id').value;
        const timeSelect = document.getElementById('appointment_time');

        if (!date) return;

        // Clear existing options
        timeSelect.innerHTML = '<option value="">Cargando horarios...</option>';
        timeSelect.disabled = true;

        const formData = new FormData();
        formData.append('date', date);
        if (manicuristId) {
            formData.append('manicurist_id', manicuristId);
        }

        // Construct URL more robustly to handle different base configurations
        let baseUrl = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
        let availabilityUrl = baseUrl + '/booking/get-availability';
        
        // Clean up double slashes except after protocol
        availabilityUrl = availabilityUrl.replace(/([^:]\/)\/+/g, '$1');

        fetch(availabilityUrl, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            timeSelect.innerHTML = '';
            
            if (data.error) {
                timeSelect.innerHTML = '<option value="">No disponible</option>';
                Utils.showToast(data.error, 'warning');
                return;
            }

            if (data.slots.length === 0) {
                timeSelect.innerHTML = '<option value="">No hay horarios disponibles</option>';
                Utils.showToast('No hay horarios disponibles para esta fecha', 'warning');
                return;
            }

            timeSelect.innerHTML = '<option value="">Selecciona un horario...</option>';
            data.slots.forEach(slot => {
                const option = document.createElement('option');
                option.value = slot;
                option.textContent = Utils.formatTime(slot);
                timeSelect.appendChild(option);
            });

            timeSelect.disabled = false;
        })
        .catch(error => {
            timeSelect.innerHTML = '<option value="">Error al cargar</option>';
            Utils.showToast('Error al cargar horarios disponibles. Intente nuevamente.', 'danger');
            console.error('Error:', error);
        });
    }

    function setupNavigationButtons() {
        // Next buttons
        document.getElementById('nextStep1').addEventListener('click', () => goToStep(2));
        document.getElementById('nextStep2').addEventListener('click', () => goToStep(3));
        document.getElementById('nextStep3').addEventListener('click', () => goToStep(4));

        // Previous buttons
        document.getElementById('prevStep2').addEventListener('click', () => goToStep(1));
        document.getElementById('prevStep3').addEventListener('click', () => goToStep(2));
        document.getElementById('prevStep4').addEventListener('click', () => goToStep(3));
    }

    function goToStep(stepNumber) {
        // Validate current step before proceeding
        if (stepNumber > currentStep && !validateCurrentStep()) {
            return;
        }

        // Hide all steps
        steps.forEach(step => {
            step.style.display = 'none';
        });

        // Show target step
        document.getElementById(`step${stepNumber}`).style.display = 'block';
        currentStep = stepNumber;

        // Update confirmation if going to step 4
        if (stepNumber === 4) {
            updateConfirmation();
        }

        // Scroll to top of form
        document.getElementById('bookingForm').scrollIntoView({ behavior: 'smooth' });
    }

    function validateCurrentStep() {
        switch (currentStep) {
            case 1:
                const phone = document.getElementById('phone').value.trim();
                const name = document.getElementById('name').value.trim();
                
                if (!phone || !name) {
                    Utils.showToast('Completa todos los campos requeridos', 'warning');
                    return false;
                }
                return true;

            case 2:
                const serviceId = document.getElementById('service_id').value;
                
                if (!serviceId) {
                    Utils.showToast('Selecciona un servicio', 'warning');
                    return false;
                }
                return true;

            case 3:
                const date = document.getElementById('appointment_date').value;
                const time = document.getElementById('appointment_time').value;
                
                if (!date || !time) {
                    Utils.showToast('Selecciona fecha y hora para la cita', 'warning');
                    return false;
                }
                return true;

            default:
                return true;
        }
    }

    function updateConfirmation() {
        // Update confirmation details
        document.getElementById('confirmName').textContent = document.getElementById('name').value;
        document.getElementById('confirmPhone').textContent = document.getElementById('phone').value;
        document.getElementById('confirmService').textContent = serviceData ? serviceData.name : '';
        document.getElementById('confirmDate').textContent = Utils.formatDate(document.getElementById('appointment_date').value);
        document.getElementById('confirmTime').textContent = Utils.formatTime(document.getElementById('appointment_time').value);
        document.getElementById('confirmPrice').textContent = serviceData ? serviceData.price.toFixed(2) : '0.00';
    }

    // Form validation
    form.addEventListener('submit', function(e) {
        if (!validateCurrentStep()) {
            e.preventDefault();
            return false;
        }

        // Show loading
        Utils.showLoading();
        
        // Let the form submit normally
        return true;
    });

    // Date input restrictions
    const dateInput = document.getElementById('appointment_date');
    if (dateInput) {
        // Set minimum date to today
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        dateInput.min = today.toISOString().split('T')[0];
        
        // Disable Sundays
        dateInput.addEventListener('change', function() {
            const selectedDate = new Date(this.value + 'T00:00:00');
            const dayOfWeek = selectedDate.getDay();
            
            if (dayOfWeek === 0) { // Sunday
                this.value = '';
                Utils.showToast('No atendemos los domingos. Selecciona otro día.', 'warning');
            }
        });
    }
});