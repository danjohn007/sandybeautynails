<?php
require_once 'app/core/Controller.php';
require_once 'app/models/Customer.php';
require_once 'app/models/Service.php';
require_once 'app/models/Manicurist.php';
require_once 'app/models/Appointment.php';

class BookingController extends Controller {
    private $customerModel;
    private $serviceModel;
    private $manicuristModel;
    private $appointmentModel;

    public function __construct() {
        parent::__construct();
        $this->customerModel = new Customer();
        $this->serviceModel = new Service();
        $this->manicuristModel = new Manicurist();
        $this->appointmentModel = new Appointment();
    }

    public function index() {
        // Get business hours information for display
        $businessDays = BUSINESS_DAYS;
        
        // Translate days to Spanish
        $dayTranslations = [
            'Monday' => 'Lunes',
            'Tuesday' => 'Martes', 
            'Wednesday' => 'Miércoles',
            'Thursday' => 'Jueves',
            'Friday' => 'Viernes',
            'Saturday' => 'Sábado',
            'Sunday' => 'Domingo'
        ];
        
        $translatedDays = array_map(function($day) use ($dayTranslations) {
            return $dayTranslations[$day] ?? $day;
        }, $businessDays);
        
        $daysStr = '';
        if (count($translatedDays) > 1) {
            $daysStr = implode(', ', array_slice($translatedDays, 0, -1)) . ' y ' . end($translatedDays);
        } else {
            $daysStr = end($translatedDays);
        }
        
        $data = [
            'title' => 'Reservar Cita - ' . APP_NAME,
            'services' => $this->serviceModel->getAll(),
            'manicurists' => $this->manicuristModel->getAll(),
            'csrfToken' => $this->generateCSRFToken(),
            'businessHours' => [
                'start' => BUSINESS_START_HOUR,
                'end' => BUSINESS_END_HOUR,
                'days' => $daysStr
            ]
        ];

        $this->view('booking/index', $data);
    }

    public function checkCustomer() {
        if (!$this->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $phone = $this->sanitize($this->input('phone'));
        
        if (empty($phone)) {
            $this->json(['error' => 'Teléfono requerido'], 400);
            return;
        }

        // Validate phone format (basic validation)
        if (!preg_match('/^\d{3}-\d{4}$|^\d{7,10}$/', $phone)) {
            $this->json(['error' => 'Formato de teléfono inválido'], 400);
            return;
        }

        $customer = $this->customerModel->findByPhone($phone);
        
        if ($customer) {
            $this->json([
                'exists' => true,
                'customer' => [
                    'id' => $customer['id'],
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'cedula' => $customer['cedula'],
                    'total_appointments' => $customer['total_appointments']
                ]
            ]);
        } else {
            $this->json(['exists' => false]);
        }
    }

    public function getAvailability() {
        if (!$this->isPost()) {
            $this->json(['error' => 'Método no permitido'], 405);
            return;
        }

        $date = $this->sanitize($this->input('date'));
        $manicuristId = $this->sanitize($this->input('manicurist_id'));

        if (empty($date)) {
            $this->json(['error' => 'Fecha requerida'], 400);
            return;
        }

        // Validate date is not in the past
        if (strtotime($date) < strtotime('today')) {
            $this->json(['error' => 'No se pueden hacer citas en fechas pasadas'], 400);
            return;
        }

        // Check if it's a business day
        $dayOfWeek = date('l', strtotime($date));
        if (!in_array($dayOfWeek, BUSINESS_DAYS)) {
            $this->json(['error' => 'No atendemos ese día de la semana'], 400);
            return;
        }

        $availableSlots = $this->appointmentModel->getAvailableSlots($date, $manicuristId);
        
        $this->json([
            'date' => $date,
            'slots' => $availableSlots
        ]);
    }

    public function submit() {
        if (!$this->isPost()) {
            $this->redirect('booking');
            return;
        }

        // Validate CSRF token
        $csrfToken = $this->input('csrf_token');
        if (!$this->validateCSRFToken($csrfToken)) {
            $_SESSION['error'] = 'Token de seguridad inválido. Intente nuevamente.';
            $this->redirect('booking');
            return;
        }

        // Validate required fields
        $requiredFields = [
            'phone' => 'Teléfono',
            'name' => 'Nombre completo',
            'service_id' => 'Servicio',
            'appointment_date' => 'Fecha de cita',
            'appointment_time' => 'Hora de cita'
        ];

        $errors = $this->validateRequired($requiredFields);
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            $this->redirect('booking');
            return;
        }

        $phone = $this->sanitize($this->input('phone'));
        $name = $this->sanitize($this->input('name'));
        $email = $this->sanitize($this->input('email'));
        $cedula = $this->sanitize($this->input('cedula'));
        $serviceId = (int)$this->input('service_id');
        $manicuristId = $this->input('manicurist_id') ? (int)$this->input('manicurist_id') : null;
        $appointmentDate = $this->sanitize($this->input('appointment_date'));
        $appointmentTime = $this->sanitize($this->input('appointment_time'));
        $notes = $this->sanitize($this->input('notes'));

        try {
            $this->db->beginTransaction();

            // Check or create customer
            $customer = $this->customerModel->findByPhone($phone);
            if ($customer) {
                $customerId = $customer['id'];
                // Update customer info if provided
                if (!empty($name) && $name !== $customer['name']) {
                    $this->customerModel->update($customerId, [
                        'name' => $name,
                        'email' => $email,
                        'cedula' => $cedula
                    ]);
                }
            } else {
                $customerId = $this->customerModel->create([
                    'phone' => $phone,
                    'name' => $name,
                    'email' => $email,
                    'cedula' => $cedula
                ]);
            }

            // Get service details
            $service = $this->serviceModel->findById($serviceId);
            if (!$service) {
                throw new Exception('Servicio no encontrado');
            }

            // Verify slot is still available
            if ($manicuristId && !$this->appointmentModel->isSlotAvailable($manicuristId, $appointmentDate, $appointmentTime)) {
                throw new Exception('El horario seleccionado ya no está disponible');
            }

            // Create appointment
            $appointmentId = $this->appointmentModel->create([
                'customer_id' => $customerId,
                'service_id' => $serviceId,
                'manicurist_id' => $manicuristId,
                'appointment_date' => $appointmentDate,
                'appointment_time' => $appointmentTime,
                'total_amount' => $service['price'],
                'notes' => $notes,
                'status' => 'pending',
                'payment_method' => 'mercado_pago'
            ]);

            $this->db->commit();

            // Redirect to payment or success page
            $_SESSION['appointment_id'] = $appointmentId;
            $this->redirect('payment/create');

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log('Booking error: ' . $e->getMessage());
            $_SESSION['error'] = $e->getMessage();
            $_SESSION['old_input'] = $_POST;
            $this->redirect('booking');
        }
    }

    public function success() {
        $appointmentId = $_SESSION['appointment_id'] ?? null;
        
        if (!$appointmentId) {
            $this->redirect('');
            return;
        }

        $appointment = $this->appointmentModel->findById($appointmentId);
        
        if (!$appointment) {
            $this->redirect('');
            return;
        }

        $data = [
            'title' => 'Cita Confirmada - ' . APP_NAME,
            'appointment' => $appointment
        ];

        // Clear session data
        unset($_SESSION['appointment_id']);

        $this->view('booking/success', $data);
    }
}