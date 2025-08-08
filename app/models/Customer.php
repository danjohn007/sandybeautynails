<?php
require_once 'app/core/Database.php';

class Customer {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function findByPhone($phone) {
        $sql = "SELECT * FROM customers WHERE phone = ?";
        return $this->db->fetch($sql, [$phone]);
    }

    public function create($data) {
        $sql = "INSERT INTO customers (phone, name, email, cedula) VALUES (?, ?, ?, ?)";
        $result = $this->db->query($sql, [
            $data['phone'],
            $data['name'],
            $data['email'] ?? null,
            $data['cedula'] ?? null
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE customers SET name = ?, email = ?, cedula = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [
            $data['name'],
            $data['email'] ?? null,
            $data['cedula'] ?? null,
            $id
        ]);
    }

    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT c.*, COUNT(a.id) as total_appointments 
                FROM customers c 
                LEFT JOIN appointments a ON c.id = a.customer_id 
                GROUP BY c.id 
                ORDER BY c.created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        }
        
        return $this->db->fetchAll($sql);
    }

    public function getFrequentCustomers($limit = 10) {
        $sql = "SELECT c.*, COUNT(a.id) as appointment_count,
                       SUM(a.total_amount) as total_spent
                FROM customers c 
                JOIN appointments a ON c.id = a.customer_id 
                WHERE a.status IN ('completed', 'paid')
                GROUP BY c.id 
                HAVING appointment_count >= 3
                ORDER BY appointment_count DESC, total_spent DESC 
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }

    public function getCustomerHistory($customerId) {
        $sql = "SELECT a.*, s.name as service_name, m.name as manicurist_name
                FROM appointments a
                JOIN services s ON a.service_id = s.id
                LEFT JOIN manicurists m ON a.manicurist_id = m.id
                WHERE a.customer_id = ?
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        return $this->db->fetchAll($sql, [$customerId]);
    }

    public function incrementAppointmentCount($customerId) {
        $sql = "UPDATE customers SET total_appointments = total_appointments + 1 WHERE id = ?";
        return $this->db->query($sql, [$customerId]);
    }
}