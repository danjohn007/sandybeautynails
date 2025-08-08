<?php
require_once 'app/core/Controller.php';
require_once 'app/models/Appointment.php';
require_once 'app/models/Customer.php';
require_once 'app/models/Service.php';
require_once 'app/models/Manicurist.php';

class AdminController extends Controller {
    private $appointmentModel;
    private $customerModel;
    private $serviceModel;
    private $manicuristModel;

    public function __construct() {
        parent::__construct();
        $this->appointmentModel = new Appointment();
        $this->customerModel = new Customer();
        $this->serviceModel = new Service();
        $this->manicuristModel = new Manicurist();
    }

    public function login() {
        if ($this->isAdminLoggedIn()) {
            $this->redirect('admin/dashboard');
            return;
        }

        $data = [
            'title' => 'Acceso Administrativo - ' . APP_NAME,
            'csrfToken' => $this->generateCSRFToken()
        ];

        $this->view('admin/login', $data);
    }

    public function authenticate() {
        if (!$this->isPost()) {
            $this->redirect('admin/login');
            return;
        }

        $csrfToken = $this->input('csrf_token');
        if (!$this->validateCSRFToken($csrfToken)) {
            $_SESSION['error'] = 'Token de seguridad inválido';
            $this->redirect('admin/login');
            return;
        }

        $username = $this->sanitize($this->input('username'));
        $password = $this->input('password');

        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Usuario y contraseña requeridos';
            $this->redirect('admin/login');
            return;
        }

        try {
            $sql = "SELECT * FROM admin_users WHERE username = ? AND active = 1";
            $user = $this->db->fetch($sql, [$username]);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_user_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_role'] = $user['role'];
                $_SESSION['admin_login_time'] = time();

                // Update last login
                $sql = "UPDATE admin_users SET last_login = NOW() WHERE id = ?";
                $this->db->query($sql, [$user['id']]);

                $this->redirect('admin/dashboard');
            } else {
                $_SESSION['error'] = 'Credenciales inválidas';
                $this->redirect('admin/login');
            }
        } catch (Exception $e) {
            error_log('Admin login error: ' . $e->getMessage());
            $_SESSION['error'] = 'Error interno del servidor';
            $this->redirect('admin/login');
        }
    }

    public function logout() {
        session_destroy();
        $this->redirect('admin/login');
    }

    public function dashboard() {
        $this->requireAdmin();

        // Get dashboard statistics
        $today = date('Y-m-d');
        $thisWeek = date('Y-m-d', strtotime('monday this week'));
        $thisMonth = date('Y-m-01');

        $todayStats = $this->appointmentModel->getDailyStats($today);
        $upcomingAppointments = $this->appointmentModel->getUpcomingAppointments(5);
        
        // Weekly revenue
        $weeklyRevenue = $this->appointmentModel->getRevenueByPeriod($thisWeek, $today);
        $monthlyRevenue = $this->appointmentModel->getRevenueByPeriod($thisMonth, $today);

        // Popular services
        $popularServices = $this->serviceModel->getPopularServices(5, $thisMonth);
        
        // Top performers
        $topPerformers = $this->manicuristModel->getTopPerformers(3, $thisMonth);

        $data = [
            'title' => 'Dashboard Administrativo - ' . APP_NAME,
            'todayStats' => $todayStats,
            'upcomingAppointments' => $upcomingAppointments,
            'weeklyRevenue' => array_sum(array_column($weeklyRevenue, 'revenue')),
            'monthlyRevenue' => array_sum(array_column($monthlyRevenue, 'revenue')),
            'popularServices' => $popularServices,
            'topPerformers' => $topPerformers
        ];

        $this->view('admin/dashboard', $data);
    }

    public function reservations() {
        $this->requireAdmin();

        $filters = [
            'date_from' => $this->input('date_from'),
            'date_to' => $this->input('date_to'),
            'manicurist_id' => $this->input('manicurist_id'),
            'service_id' => $this->input('service_id'),
            'status' => $this->input('status'),
            'payment_status' => $this->input('payment_status')
        ];

        // Remove empty filters
        $filters = array_filter($filters);

        $appointments = $this->appointmentModel->getAll($filters);
        $manicurists = $this->manicuristModel->getAll();
        $services = $this->serviceModel->getAll();

        $data = [
            'title' => 'Gestión de Reservaciones - ' . APP_NAME,
            'appointments' => $appointments,
            'manicurists' => $manicurists,
            'services' => $services,
            'filters' => $filters,
            'csrfToken' => $this->generateCSRFToken()
        ];

        $this->view('admin/reservations', $data);
    }

    public function clients() {
        $this->requireAdmin();

        $customers = $this->customerModel->getAll();
        $frequentCustomers = $this->customerModel->getFrequentCustomers();

        $data = [
            'title' => 'Gestión de Clientes - ' . APP_NAME,
            'customers' => $customers,
            'frequentCustomers' => $frequentCustomers
        ];

        $this->view('admin/clients', $data);
    }

    public function finances() {
        $this->requireAdmin();

        $dateFrom = $this->input('date_from', date('Y-m-01')); // First day of current month
        $dateTo = $this->input('date_to', date('Y-m-d')); // Today

        $revenueByPeriod = $this->appointmentModel->getRevenueByPeriod($dateFrom, $dateTo);
        $revenueByService = $this->serviceModel->getServiceRevenue($dateFrom, $dateTo);
        $revenueByManicurist = $this->appointmentModel->getAppointmentsByManicurist($dateFrom, $dateTo);

        $totalRevenue = array_sum(array_column($revenueByPeriod, 'revenue'));
        $totalAppointments = array_sum(array_column($revenueByPeriod, 'total_appointments'));

        $data = [
            'title' => 'Reportes Financieros - ' . APP_NAME,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'revenueByPeriod' => $revenueByPeriod,
            'revenueByService' => $revenueByService,
            'revenueByManicurist' => $revenueByManicurist,
            'totalRevenue' => $totalRevenue,
            'totalAppointments' => $totalAppointments
        ];

        $this->view('admin/finances', $data);
    }

    public function reports() {
        $this->requireAdmin();

        $dateFrom = $this->input('date_from', date('Y-m-01'));
        $dateTo = $this->input('date_to', date('Y-m-d'));

        $appointmentsByService = $this->appointmentModel->getAppointmentsByService($dateFrom, $dateTo);
        $appointmentsByManicurist = $this->appointmentModel->getAppointmentsByManicurist($dateFrom, $dateTo);
        $popularServices = $this->serviceModel->getPopularServices(10, $dateFrom, $dateTo);

        $data = [
            'title' => 'Reportes y Gráficas - ' . APP_NAME,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'appointmentsByService' => $appointmentsByService,
            'appointmentsByManicurist' => $appointmentsByManicurist,
            'popularServices' => $popularServices
        ];

        $this->view('admin/reports', $data);
    }

    public function updateStatus() {
        $this->requireAdmin();

        if (!$this->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $csrfToken = $this->input('csrf_token');
        if (!$this->validateCSRFToken($csrfToken)) {
            $this->json(['error' => 'Token de seguridad inválido'], 400);
            return;
        }

        $appointmentId = (int)$this->input('appointment_id');
        $status = $this->sanitize($this->input('status'));

        $validStatuses = ['pending', 'confirmed', 'paid', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            $this->json(['error' => 'Estado inválido'], 400);
            return;
        }

        try {
            $this->appointmentModel->updateStatus($appointmentId, $status);
            $this->json(['success' => true, 'message' => 'Estado actualizado correctamente']);
        } catch (Exception $e) {
            error_log('Status update error: ' . $e->getMessage());
            $this->json(['error' => 'Error al actualizar el estado'], 500);
        }
    }
}