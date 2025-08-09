<?php
require_once 'app/core/Controller.php';
require_once 'app/models/Appointment.php';

class PaymentController extends Controller {
    private $appointmentModel;

    public function __construct() {
        parent::__construct();
        $this->appointmentModel = new Appointment();
    }

    public function create() {
        $appointmentId = $_SESSION['appointment_id'] ?? null;
        
        if (!$appointmentId) {
            $this->redirect('booking');
            return;
        }

        $appointment = $this->appointmentModel->findById($appointmentId);
        
        if (!$appointment) {
            $this->redirect('booking');
            return;
        }

        // If Mercado Pago is not configured, redirect to success page
        if (empty(MP_ACCESS_TOKEN)) {
            // For demo purposes, mark as paid and redirect to success
            $this->appointmentModel->updatePaymentStatus($appointmentId, 'paid', 'DEMO_PAYMENT_' . time());
            $this->appointmentModel->updateStatus($appointmentId, 'confirmed');
            $this->redirect('booking/success');
            return;
        }

        // Create Mercado Pago payment preference
        try {
            $preference = $this->createMercadoPagoPreference($appointment);
            
            $data = [
                'title' => 'Procesar Pago - ' . APP_NAME,
                'appointment' => $appointment,
                'preference' => $preference,
                'mp_public_key' => MP_PUBLIC_KEY
            ];

            $this->view('payment/create', $data);

        } catch (Exception $e) {
            error_log('Payment creation error: ' . $e->getMessage());
            $_SESSION['error'] = 'Error al procesar el pago. Intente nuevamente.';
            $this->redirect('booking');
        }
    }

    private function createMercadoPagoPreference($appointment) {
        // This is a simplified example. In production, you would use the Mercado Pago SDK
        $preference = [
            'id' => 'demo_preference_' . $appointment['id'],
            'items' => [
                [
                    'title' => $appointment['service_name'],
                    'quantity' => 1,
                    'currency_id' => 'MXN',
                    'unit_price' => (float)$appointment['total_amount']
                ]
            ],
            'payer' => [
                'name' => $appointment['customer_name'],
                'email' => $appointment['customer_email'] ?: 'cliente@example.com',
                'phone' => [
                    'number' => $appointment['customer_phone']
                ]
            ],
            'back_urls' => [
                'success' => APP_URL . '/payment/success',
                'failure' => APP_URL . '/payment/failure',
                'pending' => APP_URL . '/payment/success'
            ],
            'auto_return' => 'approved',
            'external_reference' => $appointment['id']
        ];

        return $preference;
    }

    public function success() {
        $appointmentId = $_SESSION['appointment_id'] ?? $this->input('external_reference');
        $paymentId = $this->input('payment_id');
        $status = $this->input('status');

        if ($appointmentId) {
            try {
                // In production, verify payment status with Mercado Pago API
                $this->appointmentModel->updatePaymentStatus($appointmentId, 'paid', $paymentId);
                $this->appointmentModel->updateStatus($appointmentId, 'confirmed');
                
                $_SESSION['appointment_id'] = $appointmentId;
                $this->redirect('booking/success');
            } catch (Exception $e) {
                error_log('Payment success error: ' . $e->getMessage());
                $this->redirect('payment/failure');
            }
        } else {
            $this->redirect('booking');
        }
    }

    public function failure() {
        $appointmentId = $_SESSION['appointment_id'] ?? $this->input('external_reference');
        
        if ($appointmentId) {
            $this->appointmentModel->updatePaymentStatus($appointmentId, 'failed');
        }

        $data = [
            'title' => 'Error en el Pago - ' . APP_NAME,
            'message' => 'Hubo un problema al procesar su pago. Puede intentar nuevamente o contactarnos para asistencia.'
        ];

        $this->view('payment/failure', $data);
    }

    public function webhook() {
        // Mercado Pago webhook handler
        if (!$this->isPost()) {
            http_response_code(405);
            return;
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (isset($data['type']) && $data['type'] === 'payment') {
            $paymentId = $data['data']['id'];
            
            try {
                // In production, verify payment with Mercado Pago API
                // For now, we'll just log it
                error_log('Payment webhook received: ' . $paymentId);
                
                http_response_code(200);
                echo 'OK';
            } catch (Exception $e) {
                error_log('Webhook error: ' . $e->getMessage());
                http_response_code(500);
            }
        } else {
            http_response_code(400);
        }
    }
}