<?php
ob_start();
?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 admin-sidebar">
            <div class="py-3">
                <h5 class="text-white px-3 mb-3">
                    <i class="fas fa-user-shield me-2"></i>
                    Admin Panel
                </h5>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="<?= APP_URL ?>/admin/dashboard">
                        <i class="fas fa-chart-pie me-2"></i> Dashboard
                    </a>
                    <a class="nav-link" href="<?= APP_URL ?>/admin/reservations">
                        <i class="fas fa-calendar-check me-2"></i> Reservaciones
                    </a>
                    <a class="nav-link" href="<?= APP_URL ?>/admin/clients">
                        <i class="fas fa-users me-2"></i> Clientes
                    </a>
                    <a class="nav-link" href="<?= APP_URL ?>/admin/finances">
                        <i class="fas fa-chart-line me-2"></i> Finanzas
                    </a>
                    <a class="nav-link" href="<?= APP_URL ?>/admin/reports">
                        <i class="fas fa-chart-bar me-2"></i> Reportes
                    </a>
                    <hr class="my-3">
                    <a class="nav-link" href="<?= APP_URL ?>">
                        <i class="fas fa-home me-2"></i> Ver Sitio
                    </a>
                    <a class="nav-link" href="<?= APP_URL ?>/admin/logout">
                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                    </a>
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 admin-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1>
                    <i class="fas fa-chart-pie me-2 text-primary"></i>
                    Dashboard
                </h1>
                <div class="text-muted">
                    <i class="fas fa-user me-1"></i>
                    Bienvenido, <?= $_SESSION['admin_username'] ?>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?= $todayStats['total_appointments'] ?? 0 ?></div>
                                    <div>Citas Hoy</div>
                                </div>
                                <i class="fas fa-calendar-day fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number"><?= $todayStats['completed'] ?? 0 ?></div>
                                    <div>Completadas</div>
                                </div>
                                <i class="fas fa-check-circle fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number">$<?= number_format($weeklyRevenue ?? 0, 0) ?></div>
                                    <div>Ingresos Semana</div>
                                </div>
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card stat-card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="stat-number">$<?= number_format($monthlyRevenue ?? 0, 0) ?></div>
                                    <div>Ingresos Mes</div>
                                </div>
                                <i class="fas fa-dollar-sign fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Upcoming Appointments -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Próximas Citas
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($upcomingAppointments)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Cliente</th>
                                                <th>Servicio</th>
                                                <th>Fecha</th>
                                                <th>Hora</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($upcomingAppointments as $appointment): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= htmlspecialchars($appointment['customer_name']) ?></strong><br>
                                                        <small class="text-muted"><?= htmlspecialchars($appointment['customer_phone']) ?></small>
                                                    </td>
                                                    <td><?= htmlspecialchars($appointment['service_name']) ?></td>
                                                    <td><?= date('d/m/Y', strtotime($appointment['appointment_date'])) ?></td>
                                                    <td><?= date('H:i', strtotime($appointment['appointment_time'])) ?></td>
                                                    <td>
                                                        <span class="badge status-<?= $appointment['status'] ?>">
                                                            <?= ucfirst($appointment['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-4">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No hay citas próximas</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="col-lg-4">
                    <!-- Popular Services -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-star me-2"></i>
                                Servicios Populares
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($popularServices)): ?>
                                <?php foreach (array_slice($popularServices, 0, 5) as $service): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($service['name']) ?></div>
                                            <small class="text-muted"><?= $service['booking_count'] ?> reservas</small>
                                        </div>
                                        <span class="badge bg-primary"><?= $service['booking_count'] ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No hay datos suficientes</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Top Performers -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="fas fa-trophy me-2"></i>
                                Top Manicuristas
                            </h6>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($topPerformers)): ?>
                                <?php foreach ($topPerformers as $index => $performer): ?>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($performer['name']) ?></div>
                                            <small class="text-muted">$<?= number_format($performer['total_revenue'] ?? 0, 0) ?> ingresos</small>
                                        </div>
                                        <span class="badge bg-warning">#<?= $index + 1 ?></span>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No hay datos suficientes</p>
                            <?php endif; ?>
                        </div>
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