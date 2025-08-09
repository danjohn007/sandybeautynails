<?php
if (!class_exists('Database')) {
    require_once 'app/core/Database.php';
}

class Service {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($activeOnly = true) {
        $sql = "SELECT * FROM services" . ($activeOnly ? " WHERE active = 1" : "") . " ORDER BY name";
        return $this->db->fetchAll($sql);
    }

    public function findById($id) {
        $sql = "SELECT * FROM services WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO services (name, description, price, duration, active) VALUES (?, ?, ?, ?, ?)";
        $result = $this->db->query($sql, [
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['duration'] ?? 60,
            $data['active'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE services SET name = ?, description = ?, price = ?, duration = ?, active = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['duration'] ?? 60,
            $data['active'] ?? 1,
            $id
        ]);
    }

    public function delete($id) {
        $sql = "UPDATE services SET active = 0, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }

    public function getPopularServices($limit = 5, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT s.*, COUNT(a.id) as booking_count
                FROM services s
                JOIN appointments a ON s.id = a.service_id
                WHERE a.status IN ('confirmed', 'paid', 'completed')";
        
        $params = [];
        if ($dateFrom) {
            $sql .= " AND a.appointment_date >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $sql .= " AND a.appointment_date <= ?";
            $params[] = $dateTo;
        }
        
        $sql .= " GROUP BY s.id ORDER BY booking_count DESC LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }

    public function getServiceRevenue($dateFrom = null, $dateTo = null) {
        $sql = "SELECT s.name, s.price, COUNT(a.id) as bookings, SUM(a.total_amount) as total_revenue
                FROM services s
                JOIN appointments a ON s.id = a.service_id
                WHERE a.status IN ('paid', 'completed')";
        
        $params = [];
        if ($dateFrom) {
            $sql .= " AND a.appointment_date >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $sql .= " AND a.appointment_date <= ?";
            $params[] = $dateTo;
        }
        
        $sql .= " GROUP BY s.id ORDER BY total_revenue DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
}