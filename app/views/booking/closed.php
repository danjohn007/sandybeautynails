<?php
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-white text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Fuera de Horario
                    </h2>
                </div>
                <div class="card-body p-4 text-center">
                    <i class="fas fa-business-time text-warning" style="font-size: 4rem;"></i>
                    
                    <h3 class="mt-3 text-warning">¡Lo sentimos!</h3>
                    <p class="lead">Nuestro sistema de reservas está disponible solo durante horarios de atención.</p>

                    <div class="card bg-light mt-4">
                        <div class="card-body">
                            <h5><i class="fas fa-clock me-2"></i>Horarios de Atención</h5>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <p class="mb-2">
                                        <strong><?= $businessHours['days'] ?></strong><br>
                                        <?= $businessHours['start'] ?>:00 - <?= $businessHours['end'] ?>:00 horas
                                    </p>
                                    <p class="text-danger mb-0">
                                        <i class="fas fa-times-circle me-1"></i>
                                        <strong>Domingos:</strong> Cerrado
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-lightbulb me-2"></i>¿Sabías que...?</h6>
                        <p class="mb-0">Puedes visitar nuestro sistema durante los horarios de atención para agendar tu cita en línea de manera rápida y fácil.</p>
                    </div>

                    <div class="mt-4">
                        <h6>Mientras tanto, puedes:</h6>
                        <div class="row mt-3">
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
                    </div>

                    <div class="mt-4">
                        <a href="<?= APP_URL ?>" class="btn btn-primary btn-lg">
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