<?php
class Controller {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    protected function view($view, $data = []) {
        extract($data);
        
        $viewFile = 'app/views/' . $view . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View not found: " . $view);
        }

        include $viewFile;
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

    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    protected function input($key, $default = null) {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function sanitize($data) {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        if ($data === null) {
            return null;
        }
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    protected function validateRequired($fields) {
        $errors = [];
        foreach ($fields as $field => $label) {
            if (empty($this->input($field))) {
                $errors[$field] = $label . ' es requerido';
            }
        }
        return $errors;
    }

    protected function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token']) || 
            !isset($_SESSION['csrf_token_time']) || 
            time() - $_SESSION['csrf_token_time'] > CSRF_TOKEN_EXPIRE) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        return $_SESSION['csrf_token'];
    }

    protected function validateCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token) &&
               isset($_SESSION['csrf_token_time']) &&
               time() - $_SESSION['csrf_token_time'] <= CSRF_TOKEN_EXPIRE;
    }

    protected function isAdminLoggedIn() {
        return isset($_SESSION['admin_logged_in']) && 
               $_SESSION['admin_logged_in'] === true &&
               isset($_SESSION['admin_login_time']) &&
               time() - $_SESSION['admin_login_time'] <= ADMIN_SESSION_TIMEOUT;
    }

    protected function requireAdmin() {
        if (!$this->isAdminLoggedIn()) {
            $this->redirect('admin/login');
        }
        // Update session time
        $_SESSION['admin_login_time'] = time();
    }
}