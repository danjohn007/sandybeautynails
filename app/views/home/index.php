<?php
ob_start();
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-sparkles me-3"></i>
                    Bienvenido a Sandy Beauty Nails
                </h1>
                <p class="lead mb-4">
                    Tu salón de belleza especializado en servicios de manicure, pedicure y nail art. 
                    Agenda tu cita en línea de manera fácil y rápida.
                </p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                    <a href="<?= APP_URL ?>/booking" class="btn btn-light btn-lg px-4 me-md-2">
                        <i class="fas fa-calendar-plus me-2"></i>
                        Reservar Cita
                    </a>
                    <a href="#servicios" class="btn btn-outline-light btn-lg px-4">
                        Ver Servicios
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="hero-image">
                    <i class="fas fa-hand-sparkles display-1 text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Business Hours Alert -->
<section class="py-3 bg-info text-white">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center">
                <i class="fas fa-clock me-2"></i>
                <strong>Horarios de Atención:</strong> 
                <?= $businessHours['days'] ?> de <?= $businessHours['start'] ?>:00 a <?= $businessHours['end'] ?>:00 horas
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="servicios" class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">
            <i class="fas fa-star me-2 text-primary"></i>
            Nuestros Servicios
        </h2>
        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center service-card">
                    <div class="card-body">
                        <i class="fas fa-hand-holding-heart text-primary display-4 mb-3"></i>
                        <h5 class="card-title">Manicure</h5>
                        <p class="card-text">Cuidado completo de manos con limado, cutícula y esmaltado profesional.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center service-card">
                    <div class="card-body">
                        <i class="fas fa-shoe-prints text-primary display-4 mb-3"></i>
                        <h5 class="card-title">Pedicure</h5>
                        <p class="card-text">Tratamiento relajante para pies con cuidado de uñas y masaje revitalizante.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center service-card">
                    <div class="card-body">
                        <i class="fas fa-magic text-primary display-4 mb-3"></i>
                        <h5 class="card-title">Uñas Acrílicas</h5>
                        <p class="card-text">Extensión y decoración con técnicas profesionales para uñas duraderas.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 text-center service-card">
                    <div class="card-body">
                        <i class="fas fa-paint-brush text-primary display-4 mb-3"></i>
                        <h5 class="card-title">Nail Art</h5>
                        <p class="card-text">Diseños únicos y personalizados para expresar tu estilo personal.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-5">
            <i class="fas fa-gem me-2 text-primary"></i>
            ¿Por qué elegir Sandy Beauty Nails?
        </h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-clock text-primary display-4 mb-3"></i>
                    <h5>Reserva Online</h5>
                    <p>Sistema de citas en línea disponible 24/7. Selecciona tu servicio preferido y horario ideal.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-users text-primary display-4 mb-3"></i>
                    <h5>Personal Especializado</h5>
                    <p>Manicuristas certificadas con años de experiencia en técnicas modernas y tradicionales.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <i class="fas fa-shield-alt text-primary display-4 mb-3"></i>
                    <h5>Higiene y Seguridad</h5>
                    <p>Cumplimos con los más altos estándares de limpieza y esterilización de herramientas.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="mb-4">¿Lista para consentir tus manos y pies?</h2>
        <p class="lead mb-4">Agenda tu cita ahora y disfruta de nuestros servicios profesionales</p>
        <a href="<?= APP_URL ?>/booking" class="btn btn-light btn-lg px-5">
            <i class="fas fa-calendar-check me-2"></i>
            Reservar Mi Cita
        </a>
    </div>
</section>

<?php
$content = ob_get_clean();
include 'app/views/layouts/main.php';
?>