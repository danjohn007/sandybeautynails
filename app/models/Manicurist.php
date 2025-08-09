<?php
if (!class_exists('Database')) {
    require_once 'app/core/Database.php';
}

class Manicurist {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAll($activeOnly = true) {
        $sql = "SELECT * FROM manicurists" . ($activeOnly ? " WHERE active = 1" : "") . " ORDER BY name";
        return $this->db->fetchAll($sql);
    }

    public function findById($id) {
        $sql = "SELECT * FROM manicurists WHERE id = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function create($data) {
        $sql = "INSERT INTO manicurists (name, phone, email, specialties, active) VALUES (?, ?, ?, ?, ?)";
        $result = $this->db->query($sql, [
            $data['name'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['specialties'] ?? '',
            $data['active'] ?? 1
        ]);
        return $this->db->lastInsertId();
    }

    public function update($id, $data) {
        $sql = "UPDATE manicurists SET name = ?, phone = ?, email = ?, specialties = ?, active = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [
            $data['name'],
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['specialties'] ?? '',
            $data['active'] ?? 1,
            $id
        ]);
    }

    public function getAvailability($manicuristId, $date) {
        // Get business hours
        $startHour = BUSINESS_START_HOUR;
        $endHour = BUSINESS_END_HOUR;
        $duration = APPOINTMENT_DURATION;
        
        // Get existing appointments for this manicurist on this date
        $sql = "SELECT appointment_time, duration FROM appointments a
                JOIN services s ON a.service_id = s.id
                WHERE a.manicurist_id = ? AND a.appointment_date = ? 
                AND a.status NOT IN ('cancelled')";
        
        $appointments = $this->db->fetchAll($sql, [$manicuristId, $date]);
        
        // Generate available time slots
        $availableSlots = [];
        $bookedTimes = [];
        
        foreach ($appointments as $appointment) {
            $bookedTimes[] = $appointment['appointment_time'];
        }
        
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $timeSlot = sprintf('%02d:00:00', $hour);
            if (!in_array($timeSlot, $bookedTimes)) {
                $availableSlots[] = $timeSlot;
            }
        }
        
        return $availableSlots;
    }

    public function getPerformanceStats($manicuristId, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT 
                    COUNT(a.id) as total_appointments,
                    SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed_appointments,
                    SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_appointments,
                    SUM(a.total_amount) as total_revenue,
                    AVG(a.total_amount) as avg_appointment_value
                FROM appointments a
                WHERE a.manicurist_id = ?";
        
        $params = [$manicuristId];
        
        if ($dateFrom) {
            $sql .= " AND a.appointment_date >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $sql .= " AND a.appointment_date <= ?";
            $params[] = $dateTo;
        }
        
        return $this->db->fetch($sql, $params);
    }

    public function getTopPerformers($limit = 5, $dateFrom = null, $dateTo = null) {
        $sql = "SELECT m.*, 
                    COUNT(a.id) as total_appointments,
                    SUM(a.total_amount) as total_revenue
                FROM manicurists m
                LEFT JOIN appointments a ON m.id = a.manicurist_id 
                    AND a.status IN ('completed', 'paid')";
        
        $params = [];
        if ($dateFrom) {
            $sql .= " AND a.appointment_date >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $sql .= " AND a.appointment_date <= ?";
            $params[] = $dateTo;
        }
        
        $sql .= " WHERE m.active = 1
                  GROUP BY m.id 
                  ORDER BY total_revenue DESC, total_appointments DESC 
                  LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }
}