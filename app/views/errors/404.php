<?php
ob_start();
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center">
                    <h2 class="mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Página No Encontrada
                    </h2>
                </div>
                <div class="card-body p-4 text-center">
                    <i class="fas fa-search text-muted" style="font-size: 4rem;"></i>
                    
                    <h3 class="mt-3">Error 404</h3>
                    <p class="lead">La página que buscas no existe o ha sido movida.</p>

                    <div class="alert alert-info mt-4">
                        <h6><i class="fas fa-lightbulb me-2"></i>¿Qué puedes hacer?</h6>
                        <ul class="text-start mb-0">
                            <li>Verificar la URL en la barra de direcciones</li>
                            <li>Regresar a la página anterior</li>
                            <li>Visitar nuestra página principal</li>
                            <li>Usar el menú de navegación</li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <a href="<?= APP_URL ?>" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home me-2"></i>
                            Página Principal
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-arrow-left me-2"></i>
                            Regresar
                        </button>
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