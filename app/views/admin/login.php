<?php
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-center align-items-center" style="min-height: 80vh;">
                <div class="card shadow" style="width: 100%; max-width: 400px;">
                    <div class="card-header bg-primary text-white text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i>
                            Acceso Administrativo
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="<?= APP_URL ?>/admin/authenticate">
                            <input type="hidden" name="csrf_token" value="<?= $csrfToken ?>">
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">
                                    <i class="fas fa-user me-1"></i>
                                    Usuario
                                </label>
                                <input type="text" class="form-control form-control-lg" id="username" name="username" 
                                       placeholder="Ingresa tu usuario" required autocomplete="username">
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-1"></i>
                                    Contraseña
                                </label>
                                <input type="password" class="form-control form-control-lg" id="password" name="password" 
                                       placeholder="Ingresa tu contraseña" required autocomplete="current-password">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Ingresar
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 text-center">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Área restringida para administradores
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Demo credentials info (remove in production) -->
<div class="position-fixed bottom-0 start-0 m-3">
    <div class="card bg-info text-white" style="max-width: 300px;">
        <div class="card-body py-2">
            <small>
                <strong>Demo:</strong><br>
                Usuario: admin<br>
                Contraseña: admin123
            </small>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?>