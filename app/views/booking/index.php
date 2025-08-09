<?php
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Reservar Nueva Cita
                    </h2>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Business Hours Information -->
                    <div class="alert alert-info mb-4">
                        <h6 class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            Horarios de Atención
                        </h6>
                        <p class="mb-1">
                            <strong><?= $businessHours['days'] ?>:</strong> 
                            <?= $businessHours['start'] ?>:00 - <?= $businessHours['end'] ?>:00 horas
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            <small>Puedes solicitar tu cita en cualquier momento, pero las citas solo se pueden agendar durante nuestros horarios de atención.</small>
                        </p>
                    </div>
                    
                    <!-- Booking Form -->
                    <form id="bookingForm" method="POST" action="<?= APP_URL ?>/booking/submit">
                        <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                        
                        <!-- Step 1: Customer Information -->
                        <div class="booking-step" id="step1">
                            <h4 class="mb-4">
                                <span class="badge bg-primary me-2">1</span>
                                Información del Cliente
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-1"></i>
                                        Teléfono <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           placeholder="555-1234567" required
                                           value="<?= $_SESSION['old_input']['phone'] ?? '' ?>">
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Si ya eres cliente, tus datos se cargarán automáticamente
                                    </div>
                                    <?php if (isset($_SESSION['errors']['phone'])): ?>
                                        <div class="text-danger"><?= $_SESSION['errors']['phone'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-primary mt-4" id="checkCustomerBtn">
                                        <i class="fas fa-search me-1"></i>
                                        Verificar Cliente
                                    </button>
                                </div>
                            </div>

                            <div id="customerInfo" class="mt-4" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">
                                            <i class="fas fa-user me-1"></i>
                                            Nombre Completo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" class="form-control" id="name" name="name" required
                                               value="<?= $_SESSION['old_input']['name'] ?? '' ?>">
                                        <?php if (isset($_SESSION['errors']['name'])): ?>
                                            <div class="text-danger"><?= $_SESSION['errors']['name'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-1"></i>
                                            Correo Electrónico
                                        </label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               value="<?= $_SESSION['old_input']['email'] ?? '' ?>">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <label for="cedula" class="form-label">
                                            <i class="fas fa-id-card me-1"></i>
                                            Número de Cédula (Opcional)
                                        </label>
                                        <input type="text" class="form-control" id="cedula" name="cedula"
                                               value="<?= $_SESSION['old_input']['cedula'] ?? '' ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="button" class="btn btn-primary" id="nextStep1" disabled>
                                    Siguiente <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Service Selection -->
                        <div class="booking-step" id="step2" style="display: none;">
                            <h4 class="mb-4">
                                <span class="badge bg-primary me-2">2</span>
                                Seleccionar Servicio
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <label for="service_id" class="form-label">
                                        <i class="fas fa-star me-1"></i>
                                        Servicio <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="service_id" name="service_id" required>
                                        <option value="">Selecciona un servicio...</option>
                                        <?php foreach ($services as $service): ?>
                                            <option value="<?= $service['id'] ?>" 
                                                    data-price="<?= $service['price'] ?>"
                                                    data-duration="<?= $service['duration'] ?>"
                                                    <?= (($_SESSION['old_input']['service_id'] ?? '') == $service['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($service['name']) ?> - $<?= number_format($service['price'], 2) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (isset($_SESSION['errors']['service_id'])): ?>
                                        <div class="text-danger"><?= $_SESSION['errors']['service_id'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label for="manicurist_id" class="form-label">
                                        <i class="fas fa-user-md me-1"></i>
                                        Manicurista (Opcional)
                                    </label>
                                    <select class="form-select" id="manicurist_id" name="manicurist_id">
                                        <option value="">Cualquier disponible</option>
                                        <?php foreach ($manicurists as $manicurist): ?>
                                            <option value="<?= $manicurist['id'] ?>"
                                                    <?= (($_SESSION['old_input']['manicurist_id'] ?? '') == $manicurist['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($manicurist['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <div id="serviceDescription" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                <h6>Descripción del Servicio:</h6>
                                <p id="serviceDesc"></p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <strong>Precio:</strong> $<span id="servicePrice"></span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Duración:</strong> <span id="serviceDuration"></span> minutos
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="prevStep2">
                                    <i class="fas fa-arrow-left me-1"></i> Anterior
                                </button>
                                <button type="button" class="btn btn-primary" id="nextStep2" disabled>
                                    Siguiente <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Date & Time Selection -->
                        <div class="booking-step" id="step3" style="display: none;">
                            <h4 class="mb-4">
                                <span class="badge bg-primary me-2">3</span>
                                Fecha y Hora
                            </h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="appointment_date" class="form-label">
                                        <i class="fas fa-calendar me-1"></i>
                                        Fecha de la Cita <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                           required min="<?= date('Y-m-d') ?>"
                                           value="<?= $_SESSION['old_input']['appointment_date'] ?? '' ?>">
                                    <?php if (isset($_SESSION['errors']['appointment_date'])): ?>
                                        <div class="text-danger"><?= $_SESSION['errors']['appointment_date'] ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="col-md-6">
                                    <label for="appointment_time" class="form-label">
                                        <i class="fas fa-clock me-1"></i>
                                        Hora de la Cita <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="appointment_time" name="appointment_time" required disabled>
                                        <option value="">Selecciona primero la fecha</option>
                                    </select>
                                    <?php if (isset($_SESSION['errors']['appointment_time'])): ?>
                                        <div class="text-danger"><?= $_SESSION['errors']['appointment_time'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="mt-3">
                                <label for="notes" class="form-label">
                                    <i class="fas fa-comment me-1"></i>
                                    Notas Adicionales (Opcional)
                                </label>
                                <textarea class="form-control" id="notes" name="notes" rows="3" 
                                          placeholder="Alguna preferencia o comentario especial..."><?= $_SESSION['old_input']['notes'] ?? '' ?></textarea>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="prevStep3">
                                    <i class="fas fa-arrow-left me-1"></i> Anterior
                                </button>
                                <button type="button" class="btn btn-primary" id="nextStep3" disabled>
                                    Siguiente <i class="fas fa-arrow-right ms-1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Confirmation -->
                        <div class="booking-step" id="step4" style="display: none;">
                            <h4 class="mb-4">
                                <span class="badge bg-primary me-2">4</span>
                                Confirmar Reservación
                            </h4>
                            
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h5>Resumen de tu Cita</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Cliente:</strong> <span id="confirmName"></span></p>
                                            <p><strong>Teléfono:</strong> <span id="confirmPhone"></span></p>
                                            <p><strong>Servicio:</strong> <span id="confirmService"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Fecha:</strong> <span id="confirmDate"></span></p>
                                            <p><strong>Hora:</strong> <span id="confirmTime"></span></p>
                                            <p><strong>Total:</strong> $<span id="confirmPrice"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Método de Pago:</strong> Después de confirmar tu cita, serás redirigido a Mercado Pago para completar el pago de forma segura.
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="prevStep4">
                                    <i class="fas fa-arrow-left me-1"></i> Anterior
                                </button>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="fas fa-check me-2"></i>
                                    Confirmar Cita
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <div class="mt-3">Procesando...</div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

// Additional JavaScript for booking functionality
$additionalScripts = '<script src="' . APP_URL . '/public/js/booking.js"></script>';

include 'app/views/layouts/main.php';

// Clear old session data
unset($_SESSION['old_input'], $_SESSION['errors']);
?>