<?php
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-success text-white text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        ¡Cita Confirmada!
                    </h2>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h3 class="mt-3 text-success">¡Reservación Exitosa!</h3>
                        <p class="lead">Tu cita ha sido confirmada correctamente</p>
                    </div>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="text-center mb-4">Detalles de tu Cita</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-user me-2"></i>Cliente:</strong><br>
                                       <?= htmlspecialchars($appointment['customer_name']) ?></p>
                                    
                                    <p><strong><i class="fas fa-phone me-2"></i>Teléfono:</strong><br>
                                       <?= htmlspecialchars($appointment['customer_phone']) ?></p>
                                    
                                    <p><strong><i class="fas fa-star me-2"></i>Servicio:</strong><br>
                                       <?= htmlspecialchars($appointment['service_name']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong><i class="fas fa-calendar me-2"></i>Fecha:</strong><br>
                                       <?= date('l, d \d\e F \d\e Y', strtotime($appointment['appointment_date'])) ?></p>
                                    
                                    <p><strong><i class="fas fa-clock me-2"></i>Hora:</strong><br>
                                       <?= date('h:i A', strtotime($appointment['appointment_time'])) ?></p>
                                    
                                    <p><strong><i class="fas fa-dollar-sign me-2"></i>Total:</strong><br>
                                       $<?= number_format($appointment['total_amount'], 2) ?></p>
                                </div>
                            </div>

                            <?php if (!empty($appointment['manicurist_name'])): ?>
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong><i class="fas fa-user-md me-2"></i>Manicurista asignada:</strong><br>
                                           <?= htmlspecialchars($appointment['manicurist_name']) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="text-center mt-4">
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check me-1"></i>
                                    Estado: <?= ucfirst($appointment['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>Información Importante:</h6>
                        <ul class="mb-0">
                            <li>Por favor llega 10 minutos antes de tu cita</li>
                            <li>Si necesitas cancelar o reprogramar, contáctanos con al menos 24 horas de anticipación</li>
                            <li>Trae una identificación válida</li>
                            <li>El pago se completó exitosamente</li>
                        </ul>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h6><i class="fas fa-map-marker-alt me-2"></i>Ubicación</h6>
                                    <p class="mb-0">Sandy Beauty Nails<br>
                                    Dirección del salón<br>
                                    Ciudad, Estado</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-secondary text-white">
                                <div class="card-body text-center">
                                    <h6><i class="fas fa-phone me-2"></i>Contacto</h6>
                                    <p class="mb-0">Teléfono: (555) 123-4567<br>
                                    WhatsApp: (555) 123-4567<br>
                                    Email: info@sandybeautynails.com</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= APP_URL ?>" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home me-2"></i>
                            Volver al Inicio
                        </a>
                        <a href="<?= APP_URL ?>/booking" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            Agendar Otra Cita
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Add confetti effect
    document.addEventListener('DOMContentLoaded', function() {
        // Simple confetti effect
        const colors = ['#e91e63', '#9c27b0', '#673ab7', '#3f51b5', '#2196f3'];
        
        function createConfetti() {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * window.innerWidth + 'px';
            confetti.style.top = '-10px';
            confetti.style.zIndex = '9999';
            confetti.style.borderRadius = '50%';
            confetti.style.pointerEvents = 'none';
            
            document.body.appendChild(confetti);
            
            let fallSpeed = Math.random() * 3 + 2;
            let rotation = 0;
            
            const fall = setInterval(() => {
                confetti.style.top = parseFloat(confetti.style.top) + fallSpeed + 'px';
                rotation += 5;
                confetti.style.transform = `rotate(${rotation}deg)`;
                
                if (parseFloat(confetti.style.top) > window.innerHeight) {
                    clearInterval(fall);
                    confetti.remove();
                }
            }, 20);
        }
        
        // Create confetti for 3 seconds
        const confettiInterval = setInterval(createConfetti, 100);
        setTimeout(() => {
            clearInterval(confettiInterval);
        }, 3000);
    });
</script>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?>