<?php
require_once 'app/core/Database.php';

class Appointment {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function create($data) {
        $sql = "INSERT INTO appointments (customer_id, service_id, manicurist_id, appointment_date, appointment_time, total_amount, notes, status, payment_method) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $result = $this->db->query($sql, [
            $data['customer_id'],
            $data['service_id'],
            $data['manicurist_id'] ?? null,
            $data['appointment_date'],
            $data['appointment_time'],
            $data['total_amount'],
            $data['notes'] ?? '',
            $data['status'] ?? 'pending',
            $data['payment_method'] ?? 'mercado_pago'
        ]);
        
        return $this->db->lastInsertId();
    }

    public function findById($id) {
        $sql = "SELECT a.*, c.name as customer_name, c.phone as customer_phone, c.email as customer_email,
                       s.name as service_name, s.price as service_price, s.duration as service_duration,
                       m.name as manicurist_name
                FROM appointments a
                JOIN customers c ON a.customer_id = c.id
                JOIN services s ON a.service_id = s.id
                LEFT JOIN manicurists m ON a.manicurist_id = m.id
                WHERE a.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }

    public function getAll($filters = []) {
        $sql = "SELECT a.*, c.name as customer_name, c.phone as customer_phone,
                       s.name as service_name, s.price as service_price,
                       m.name as manicurist_name
                FROM appointments a
                JOIN customers c ON a.customer_id = c.id
                JOIN services s ON a.service_id = s.id
                LEFT JOIN manicurists m ON a.manicurist_id = m.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['date_from'])) {
            $sql .= " AND a.appointment_date >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $sql .= " AND a.appointment_date <= ?";
            $params[] = $filters['date_to'];
        }
        
        if (!empty($filters['manicurist_id'])) {
            $sql .= " AND a.manicurist_id = ?";
            $params[] = $filters['manicurist_id'];
        }
        
        if (!empty($filters['service_id'])) {
            $sql .= " AND a.service_id = ?";
            $params[] = $filters['service_id'];
        }
        
        if (!empty($filters['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filters['status'];
        }
        
        if (!empty($filters['payment_status'])) {
            $sql .= " AND a.payment_status = ?";
            $params[] = $filters['payment_status'];
        }
        
        $sql .= " ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT ?";
            $params[] = $filters['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE appointments SET status = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [$status, $id]);
    }

    public function updatePaymentStatus($id, $paymentStatus, $paymentId = null) {
        $sql = "UPDATE appointments SET payment_status = ?, payment_id = ?, updated_at = NOW() WHERE id = ?";
        return $this->db->query($sql, [$paymentStatus, $paymentId, $id]);
    }

    public function isSlotAvailable($manicuristId, $date, $time) {
        $sql = "SELECT COUNT(*) as count FROM appointments 
                WHERE manicurist_id = ? AND appointment_date = ? AND appointment_time = ? 
                AND status NOT IN ('cancelled')";
        
        $result = $this->db->fetch($sql, [$manicuristId, $date, $time]);
        return $result['count'] == 0;
    }

    public function getAvailableSlots($date, $manicuristId = null) {
        $dayOfWeek = date('l', strtotime($date));
        
        // Check if it's a business day
        if (!in_array($dayOfWeek, BUSINESS_DAYS)) {
            return [];
        }
        
        $startHour = BUSINESS_START_HOUR;
        $endHour = BUSINESS_END_HOUR;
        
        // Get booked slots
        $sql = "SELECT appointment_time FROM appointments 
                WHERE appointment_date = ? AND status NOT IN ('cancelled')";
        $params = [$date];
        
        if ($manicuristId) {
            $sql .= " AND manicurist_id = ?";
            $params[] = $manicuristId;
        }
        
        $bookedSlots = $this->db->fetchAll($sql, $params);
        $bookedTimes = array_column($bookedSlots, 'appointment_time');
        
        // Generate available slots
        $availableSlots = [];
        for ($hour = $startHour; $hour < $endHour; $hour++) {
            $timeSlot = sprintf('%02d:00:00', $hour);
            if (!in_array($timeSlot, $bookedTimes)) {
                $availableSlots[] = $timeSlot;
            }
        }
        
        return $availableSlots;
    }

    public function getDailyStats($date) {
        $sql = "SELECT 
                    COUNT(*) as total_appointments,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                    SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as daily_revenue
                FROM appointments 
                WHERE appointment_date = ?";
        
        return $this->db->fetch($sql, [$date]);
    }

    public function getRevenueByPeriod($dateFrom, $dateTo, $groupBy = 'day') {
        $dateFormat = $groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';
        
        $sql = "SELECT 
                    DATE_FORMAT(appointment_date, '$dateFormat') as period,
                    COUNT(*) as total_appointments,
                    SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END) as revenue
                FROM appointments 
                WHERE appointment_date BETWEEN ? AND ?
                GROUP BY period
                ORDER BY period";
        
        return $this->db->fetchAll($sql, [$dateFrom, $dateTo]);
    }

    public function getAppointmentsByService($dateFrom = null, $dateTo = null) {
        $sql = "SELECT s.name, COUNT(a.id) as appointment_count, SUM(a.total_amount) as total_revenue
                FROM appointments a
                JOIN services s ON a.service_id = s.id
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
        
        $sql .= " GROUP BY s.id ORDER BY appointment_count DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    public function getAppointmentsByManicurist($dateFrom = null, $dateTo = null) {
        $sql = "SELECT m.name, COUNT(a.id) as appointment_count, SUM(a.total_amount) as total_revenue
                FROM appointments a
                LEFT JOIN manicurists m ON a.manicurist_id = m.id
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
        
        $sql .= " GROUP BY a.manicurist_id ORDER BY appointment_count DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    public function getUpcomingAppointments($limit = 10) {
        $sql = "SELECT a.*, c.name as customer_name, c.phone as customer_phone,
                       s.name as service_name, m.name as manicurist_name
                FROM appointments a
                JOIN customers c ON a.customer_id = c.id
                JOIN services s ON a.service_id = s.id
                LEFT JOIN manicurists m ON a.manicurist_id = m.id
                WHERE a.appointment_date >= CURDATE() AND a.status NOT IN ('cancelled', 'completed')
                ORDER BY a.appointment_date ASC, a.appointment_time ASC
                LIMIT ?";
        
        return $this->db->fetchAll($sql, [$limit]);
    }
}