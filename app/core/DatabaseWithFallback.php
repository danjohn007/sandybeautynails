<?php
class DatabaseWithFallback {
    private static $instance = null;
    private $connection;
    private $dbType = 'unknown';

    private function __construct() {
        $this->initializeConnection();
    }

    private function initializeConnection() {
        // Try MySQL first if configured
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5 // 5 second timeout
            ]);
            $this->dbType = 'mysql';
            error_log("Database connection successful: MySQL");
            return;
        } catch (PDOException $e) {
            error_log("MySQL connection failed: " . $e->getMessage());
            
            // Try SQLite fallback if enabled
            if (defined('ENABLE_DB_FALLBACK') && ENABLE_DB_FALLBACK) {
                try {
                    $dbPath = dirname(dirname(__FILE__)) . '/storage/sandy_beauty_nails.db';
                    
                    // Ensure directory exists
                    if (!file_exists(dirname($dbPath))) {
                        mkdir(dirname($dbPath), 0755, true);
                    }
                    
                    $this->connection = new PDO("sqlite:$dbPath", null, null, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]);
                    
                    $this->dbType = 'sqlite_fallback';
                    $this->initializeSQLiteTables();
                    error_log("Database connection successful: SQLite fallback");
                    return;
                } catch (PDOException $e2) {
                    error_log("SQLite fallback also failed: " . $e2->getMessage());
                }
            }
            
            // If we reach here, both connections failed
            throw new PDOException("All database connections failed. MySQL: " . $e->getMessage());
        }
    }

    private function initializeSQLiteTables() {
        // Check if main tables exist
        $tables = $this->connection->query("SELECT name FROM sqlite_master WHERE type='table' AND name='services'")->fetchAll(PDO::FETCH_COLUMN);
        
        if (empty($tables)) {
            // Load demo database schema
            require_once dirname(__FILE__) . '/DemoDatabase.php';
            $demo = new DemoDatabase();
            // Copy the table creation logic (simplified for production)
            $this->createBasicTables();
        }
    }

    private function createBasicTables() {
        $sql = "
        CREATE TABLE IF NOT EXISTS services (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            description TEXT,
            price DECIMAL(10,2) NOT NULL,
            duration INTEGER NOT NULL DEFAULT 60,
            active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS manicurists (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            phone VARCHAR(20),
            email VARCHAR(100),
            specialties TEXT,
            active BOOLEAN DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            phone VARCHAR(20) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            cedula VARCHAR(20),
            total_appointments INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

        CREATE TABLE IF NOT EXISTS appointments (
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
        ";

        $this->connection->exec($sql);
        
        // Insert basic data if empty
        $serviceCount = $this->connection->query("SELECT COUNT(*) FROM services")->fetchColumn();
        if ($serviceCount == 0) {
            $services = [
                ['Manicure Básico', 'Limado, cutícula y esmaltado básico', 250.00, 60],
                ['Manicure Completo', 'Limado, cutícula, masaje y esmaltado', 350.00, 90],
                ['Pedicure Básico', 'Limado, cutícula y esmaltado de pies', 300.00, 75],
                ['Pedicure Completo', 'Limado, cutícula, masaje y esmaltado de pies', 450.00, 120],
            ];

            $stmt = $this->connection->prepare("INSERT INTO services (name, description, price, duration) VALUES (?, ?, ?, ?)");
            foreach ($services as $service) {
                $stmt->execute($service);
            }
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

    public function getDbType() {
        return $this->dbType;
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log("Database query error: " . $e->getMessage() . " (DB Type: " . $this->dbType . ")");
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
?>