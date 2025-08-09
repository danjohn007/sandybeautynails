<?php
session_start();

// Demo mode - simple static data
define('DEMO_MODE', true);
define('APP_URL', 'http://localhost:8080');
define('APP_NAME', 'Sandy Beauty Nails');
define('APP_TIMEZONE', 'America/Mexico_City');
define('BUSINESS_START_HOUR', 8);
define('BUSINESS_END_HOUR', 19);
define('BUSINESS_DAYS', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);

date_default_timezone_set(APP_TIMEZONE);

// Simple mock data for demo
class MockDatabase {
    private static $instance = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function fetchAll($sql, $params = []) {
        // Return mock data based on SQL
        if (strpos($sql, 'services') !== false) {
            return [
                ['id' => 1, 'name' => 'Manicure Básico', 'description' => 'Limado, cutícula y esmaltado básico', 'price' => 250.00, 'duration' => 60, 'active' => 1],
                ['id' => 2, 'name' => 'Manicure Completo', 'description' => 'Limado, cutícula, masaje y esmaltado', 'price' => 350.00, 'duration' => 90, 'active' => 1],
                ['id' => 3, 'name' => 'Pedicure Básico', 'description' => 'Limado, cutícula y esmaltado de pies', 'price' => 300.00, 'duration' => 75, 'active' => 1],
                ['id' => 4, 'name' => 'Uñas Acrílicas', 'description' => 'Extensión y decoración con acrílico', 'price' => 800.00, 'duration' => 180, 'active' => 1],
            ];
        } elseif (strpos($sql, 'manicurists') !== false) {
            return [
                ['id' => 1, 'name' => 'Sandy Rodriguez', 'phone' => '555-0101', 'email' => 'sandy@sandybeautynails.com', 'active' => 1],
                ['id' => 2, 'name' => 'María González', 'phone' => '555-0102', 'email' => 'maria@sandybeautynails.com', 'active' => 1],
                ['id' => 3, 'name' => 'Ana Martínez', 'phone' => '555-0103', 'email' => 'ana@sandybeautynails.com', 'active' => 1],
            ];
        } elseif (strpos($sql, 'appointments') !== false && strpos($sql, 'upcoming') !== false) {
            return [
                [
                    'id' => 1,
                    'customer_name' => 'Laura Hernández',
                    'customer_phone' => '555-1001',
                    'service_name' => 'Manicure Básico',
                    'manicurist_name' => 'Sandy Rodriguez',
                    'appointment_date' => date('Y-m-d', strtotime('+1 day')),
                    'appointment_time' => '09:00:00',
                    'status' => 'confirmed',
                    'total_amount' => 250.00
                ]
            ];
        }
        return [];
    }
    
    public function fetch($sql, $params = []) {
        if (strpos($sql, 'admin_users') !== false) {
            return [
                'id' => 1,
                'username' => 'admin',
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // admin123
                'email' => 'admin@sandybeautynails.com',
                'role' => 'admin',
                'active' => 1
            ];
        } elseif (strpos($sql, 'customers') !== false && strpos($sql, 'phone') !== false) {
            // Mock customer check
            if (in_array('555-1001', $params)) {
                return [
                    'id' => 1,
                    'name' => 'Laura Hernández',
                    'phone' => '555-1001',
                    'email' => 'laura.hernandez@email.com',
                    'cedula' => '12345678901',
                    'total_appointments' => 3
                ];
            }
            return null;
        } elseif (strpos($sql, 'getDailyStats') !== false || strpos($sql, 'total_appointments') !== false) {
            return [
                'total_appointments' => 5,
                'completed' => 3,
                'cancelled' => 0,
                'daily_revenue' => 1200.00
            ];
        }
        return null;
    }
    
    public function query($sql, $params = []) {
        return $this;
    }
    
    public function lastInsertId() {
        return rand(100, 999);
    }
    
    public function beginTransaction() { return true; }
    public function commit() { return true; }
    public function rollBack() { return true; }
}

// Override Database class
class_alias('MockDatabase', 'Database');

// Include core files
require_once 'app/core/Router.php';

// Simple controller base
class Controller {
    protected $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    protected function view($view, $data = []) {
        extract($data);
        $viewFile = 'app/views/' . $view . '.php';
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "View not found: $view";
        }
    }
    
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
    
    protected function redirect($url) {
        header('Location: ' . APP_URL . '/' . ltrim($url, '/'));
        exit;
    }
    
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
    
    protected function sanitize($data) {
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }
    
    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    protected function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    protected function isAdminLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
    }
    
    protected function requireAdmin() {
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('admin/login');
        }
    }
}

// Simple router
$router = new Router();
$router->add('', 'HomeController@index');
$router->add('booking', 'BookingController@index');
$router->add('admin', 'AdminController@login');
$router->add('admin/login', 'AdminController@login');
$router->add('admin/authenticate', 'AdminController@authenticate');
$router->add('admin/dashboard', 'AdminController@dashboard');

// Simple controllers
class HomeController extends Controller {
    public function index() {
        $data = [
            'title' => 'Bienvenido a ' . APP_NAME,
            'businessHours' => [
                'start' => BUSINESS_START_HOUR,
                'end' => BUSINESS_END_HOUR,
                'days' => 'Lunes a Sábado'
            ]
        ];
        $this->view('home/index', $data);
    }
}

class BookingController extends Controller {
    public function index() {
        $data = [
            'title' => 'Reservar Cita - ' . APP_NAME,
            'services' => $this->db->fetchAll('SELECT * FROM services WHERE active = 1'),
            'manicurists' => $this->db->fetchAll('SELECT * FROM manicurists WHERE active = 1'),
            'csrfToken' => $this->generateCSRFToken()
        ];
        $this->view('booking/index', $data);
    }
}

class AdminController extends Controller {
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
        
        $username = $this->input('username');
        $password = $this->input('password');
        
        if ($username === 'admin' && $password === 'admin123') {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = 'admin';
            $this->redirect('admin/dashboard');
        } else {
            $_SESSION['error'] = 'Credenciales inválidas';
            $this->redirect('admin/login');
        }
    }
    
    public function dashboard() {
        $this->requireAdmin();
        
        $data = [
            'title' => 'Dashboard Administrativo - ' . APP_NAME,
            'todayStats' => $this->db->fetch('SELECT * FROM stats'),
            'upcomingAppointments' => $this->db->fetchAll('SELECT * FROM appointments WHERE upcoming = 1'),
            'weeklyRevenue' => 3500,
            'monthlyRevenue' => 15000,
            'popularServices' => [],
            'topPerformers' => []
        ];
        
        $this->view('admin/dashboard', $data);
    }
}

// Get route and dispatch
$route = $_GET['route'] ?? '';
try {
    $router->dispatch($route);
} catch (Exception $e) {
    http_response_code(404);
    echo "<h1>Page Not Found</h1><p>Route: $route</p><p>Error: " . $e->getMessage() . "</p>";
}
?>