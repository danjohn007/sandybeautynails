-- Sandy Beauty Nails Database Schema
-- Version: 1.0
-- MySQL 5.7+

CREATE DATABASE IF NOT EXISTS sandy_beauty_nails CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sandy_beauty_nails;

-- Services table
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    duration INT NOT NULL DEFAULT 60, -- minutes
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Manicurists table
CREATE TABLE manicurists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100),
    specialties TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Customers table
CREATE TABLE customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone VARCHAR(20) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    cedula VARCHAR(20),
    total_appointments INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_phone (phone)
);

-- Appointments table
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    manicurist_id INT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'paid', 'completed', 'cancelled') DEFAULT 'pending',
    payment_method ENUM('cash', 'card', 'mercado_pago') DEFAULT 'mercado_pago',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_id VARCHAR(100),
    total_amount DECIMAL(10,2) NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    FOREIGN KEY (manicurist_id) REFERENCES manicurists(id) ON DELETE SET NULL,
    UNIQUE KEY unique_appointment (manicurist_id, appointment_date, appointment_time),
    INDEX idx_date (appointment_date),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status)
);

-- Admin users table
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    role ENUM('admin', 'manager') DEFAULT 'admin',
    active BOOLEAN DEFAULT TRUE,
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Payment transactions table
CREATE TABLE payment_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    transaction_id VARCHAR(100) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'approved', 'rejected', 'cancelled', 'refunded') DEFAULT 'pending',
    gateway_response TEXT,
    processed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    INDEX idx_transaction_id (transaction_id),
    INDEX idx_status (status)
);

-- Insert sample data

-- Services
INSERT INTO services (name, description, price, duration) VALUES
('Manicure Básico', 'Limado, cutícula y esmaltado básico', 250.00, 60),
('Manicure Completo', 'Limado, cutícula, masaje y esmaltado', 350.00, 90),
('Pedicure Básico', 'Limado, cutícula y esmaltado de pies', 300.00, 75),
('Pedicure Completo', 'Limado, cutícula, masaje y esmaltado de pies', 450.00, 120),
('Uñas Acrílicas', 'Extensión y decoración con acrílico', 800.00, 180),
('Uñas de Gel', 'Aplicación de gel y diseño', 600.00, 150),
('Manicure + Pedicure', 'Servicio completo de manos y pies', 650.00, 180),
('Diseño Artístico', 'Decoración personalizada y nail art', 400.00, 120);

-- Manicurists
INSERT INTO manicurists (name, phone, email, specialties) VALUES
('Sandy Rodriguez', '555-0101', 'sandy@sandybeautynails.com', 'Especialista en uñas acrílicas y nail art'),
('María González', '555-0102', 'maria@sandybeautynails.com', 'Manicure y pedicure tradicional'),
('Ana Martínez', '555-0103', 'ana@sandybeautynails.com', 'Uñas de gel y diseños modernos'),
('Carmen López', '555-0104', 'carmen@sandybeautynails.com', 'Servicios integrales y masajes');

-- Sample customers
INSERT INTO customers (phone, name, email, cedula) VALUES
('555-1001', 'Laura Hernández', 'laura.hernandez@email.com', '12345678901'),
('555-1002', 'Patricia Silva', 'patricia.silva@email.com', '98765432109'),
('555-1003', 'Gabriela Morales', 'gaby.morales@email.com', '45678912345'),
('555-1004', 'Rosa Jiménez', 'rosa.jimenez@email.com', '78912345678'),
('555-1005', 'Isabel Vargas', 'isabel.vargas@email.com', NULL);

-- Sample appointments
INSERT INTO appointments (customer_id, service_id, manicurist_id, appointment_date, appointment_time, status, total_amount) VALUES
(1, 1, 1, CURDATE() + INTERVAL 1 DAY, '09:00:00', 'confirmed', 250.00),
(2, 3, 2, CURDATE() + INTERVAL 1 DAY, '11:00:00', 'pending', 300.00),
(3, 5, 1, CURDATE() + INTERVAL 2 DAY, '14:00:00', 'confirmed', 800.00),
(4, 2, 3, CURDATE() + INTERVAL 3 DAY, '10:00:00', 'pending', 350.00),
(5, 7, 4, CURDATE() + INTERVAL 3 DAY, '16:00:00', 'paid', 650.00);

-- Admin user (password: admin123 - change in production!)
INSERT INTO admin_users (username, password, email, role) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@sandybeautynails.com', 'admin');

-- Update customer appointment counts
UPDATE customers SET total_appointments = (
    SELECT COUNT(*) FROM appointments WHERE customer_id = customers.id
);