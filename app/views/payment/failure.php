<?php
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error en el Pago
                    </h2>
                </div>
                <div class="card-body p-4 text-center">
                    <i class="fas fa-times-circle text-danger" style="font-size: 4rem;"></i>
                    
                    <h3 class="mt-3 text-danger">Pago No Procesado</h3>
                    <p class="lead"><?= $message ?? 'Hubo un problema al procesar tu pago.' ?></p>

                    <div class="alert alert-warning mt-4">
                        <h6><i class="fas fa-info-circle me-2"></i>¿Qué puedes hacer?</h6>
                        <ul class="text-start mb-0">
                            <li>Verificar los datos de tu tarjeta</li>
                            <li>Asegurarte de tener fondos suficientes</li>
                            <li>Intentar nuevamente en unos minutos</li>
                            <li>Contactarnos para asistencia</li>
                        </ul>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="card bg-primary text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fas fa-phone fa-2x mb-2"></i>
                                    <h6>Llamarnos</h6>
                                    <p class="mb-0">(555) 123-4567</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white h-100">
                                <div class="card-body text-center">
                                    <i class="fab fa-whatsapp fa-2x mb-2"></i>
                                    <h6>WhatsApp</h6>
                                    <p class="mb-0">(555) 123-4567</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="<?= APP_URL ?>/booking" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-redo me-2"></i>
                            Intentar Nuevamente
                        </a>
                        <a href="<?= APP_URL ?>" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-home me-2"></i>
                            Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?>