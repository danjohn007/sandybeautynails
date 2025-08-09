<?php
class DemoDatabase {
    private static $instance = null;
    private $connection;

    private function __construct() {
        try {
            // Use SQLite for demo
            $dbPath = dirname(dirname(__DIR__)) . '/storage/sandy_beauty_nails.db';
            
            // Ensure directory exists
            if (!file_exists(dirname($dbPath))) {
                mkdir(dirname($dbPath), 0755, true);
            }
            
            $this->connection = new PDO("sqlite:$dbPath", null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            
            // Initialize demo data
            $this->initializeDemo();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    private function initializeDemo() {
        // Check if services table exists (the main table we need)
        $tables = $this->connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name='services'")->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            $this->createTables();
            $this->insertSampleData();
        }
    }

    private function createTables() {
        $sql = "
        CREATE TABLE services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            duration INTEGER NOT NULL DEFAULT 60,
            active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE manicurists (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            email VARCHAR(100),
            specialties TEXT,
            active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            phone VARCHAR(20) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            cedula VARCHAR(20),
            total_appointments INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE appointments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            customer_id INTEGER NOT NULL,
            service_id INTEGER NOT NULL,
            manicurist_id INTEGER,
            appointment_date DATE NOT NULL,
            appointment_time TIME NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            payment_method VARCHAR(20) DEFAULT 'mercado_pago',
            payment_status VARCHAR(20) DEFAULT 'pending',
            payment_id VARCHAR(100),
            total_amount DECIMAL(10,2) NOT NULL,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (customer_id) REFERENCES customers(id),
            FOREIGN KEY (service_id) REFERENCES services(id),
            FOREIGN KEY (manicurist_id) REFERENCES manicurists(id)
        );

        CREATE TABLE admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            email VARCHAR(100),
            role VARCHAR(20) DEFAULT 'admin',
            active BOOLEAN DEFAULT 1,
            last_login DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE payment_transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            appointment_id INTEGER NOT NULL,
            transaction_id VARCHAR(100) NOT NULL,
            payment_method VARCHAR(50) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            gateway_response TEXT,
            processed_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (appointment_id) REFERENCES appointments(id)
        );
        ";

        $this->connection->exec($sql);
    }

    private function insertSampleData() {
        // Services
        $services = [
            ['Manicure Básico', 'Limado, cutícula y esmaltado básico', 250.00, 60],
            ['Manicure Completo', 'Limado, cutícula, masaje y esmaltado', 350.00, 90],
            ['Pedicure Básico', 'Limado, cutícula y esmaltado de pies', 300.00, 75],
            ['Pedicure Completo', 'Limado, cutícula, masaje y esmaltado de pies', 450.00, 120],
            ['Uñas Acrílicas', 'Extensión y decoración con acrílico', 800.00, 180],
            ['Uñas de Gel', 'Aplicación de gel y diseño', 600.00, 150],
            ['Manicure + Pedicure', 'Servicio completo de manos y pies', 650.00, 180],
            ['Diseño Artístico', 'Decoración personalizada y nail art', 400.00, 120]
        ];

        $stmt = $this->connection->prepare("INSERT INTO services (name, description, price, duration) VALUES (?, ?, ?, ?)");
        foreach ($services as $service) {
            $stmt->execute($service);
        }

        // Manicurists
        $manicurists = [
            ['Sandy Rodriguez', '555-0101', 'sandy@sandybeautynails.com', 'Especialista en uñas acrílicas y nail art'],
            ['María González', '555-0102', 'maria@sandybeautynails.com', 'Manicure y pedicure tradicional'],
            ['Ana Martínez', '555-0103', 'ana@sandybeautynails.com', 'Uñas de gel y diseños modernos'],
            ['Carmen López', '555-0104', 'carmen@sandybeautynails.com', 'Servicios integrales y masajes']
        ];

        $stmt = $this->connection->prepare("INSERT INTO manicurists (name, phone, email, specialties) VALUES (?, ?, ?, ?)");
        foreach ($manicurists as $manicurist) {
            $stmt->execute($manicurist);
        }

        // Sample customers
        $customers = [
            ['555-1001', 'Laura Hernández', 'laura.hernandez@email.com', '12345678901'],
            ['555-1002', 'Patricia Silva', 'patricia.silva@email.com', '98765432109'],
            ['555-1003', 'Gabriela Morales', 'gaby.morales@email.com', '45678912345'],
            ['555-1004', 'Rosa Jiménez', 'rosa.jimenez@email.com', '78912345678'],
            ['555-1005', 'Isabel Vargas', 'isabel.vargas@email.com', null]
        ];

        $stmt = $this->connection->prepare("INSERT INTO customers (phone, name, email, cedula) VALUES (?, ?, ?, ?)");
        foreach ($customers as $customer) {
            $stmt->execute($customer);
        }

        // Admin user (password: admin123)
        $stmt = $this->connection->prepare("INSERT INTO admin_users (username, password, email, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sandybeautynails.com', 'admin']);

        // Sample appointments
        $appointments = [
            [1, 1, 1, date('Y-m-d', strtotime('+1 day')), '09:00:00', 'confirmed', 250.00],
            [2, 3, 2, date('Y-m-d', strtotime('+1 day')), '11:00:00', 'pending', 300.00],
            [3, 5, 1, date('Y-m-d', strtotime('+2 days')), '14:00:00', 'confirmed', 800.00],
            [4, 2, 3, date('Y-m-d', strtotime('+3 days')), '10:00:00', 'pending', 350.00],
            [5, 7, 4, date('Y-m-d', strtotime('+3 days')), '16:00:00', 'paid', 650.00]
        ];

        $stmt = $this->connection->prepare("INSERT INTO appointments (customer_id, service_id, manicurist_id, appointment_date, appointment_time, status, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?)");
        foreach ($appointments as $appointment) {
            $stmt->execute($appointment);
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage());
            throw $e;
        }
    }

    public function fetch($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId() {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    public function commit() {
        return $this->connection->commit();
    }

    public function rollBack() {
        return $this->connection->rollBack();
    }
}