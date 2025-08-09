<?php
require_once 'app/core/Controller.php';

class HomeController extends Controller {
    
    public function index() {
        // BUSINESS_DAYS ya es un array, no necesitas explode
        $businessDays = BUSINESS_DAYS;
        $daysStr = implode(', ', array_slice($businessDays, 0, -1)) . ' y ' . end($businessDays);

        $data = [
            'title' => 'Bienvenido a ' . APP_NAME,
            'businessHours' => [
                'start' => BUSINESS_START_HOUR,
                'end' => BUSINESS_END_HOUR,
                'days' => $daysStr
            ]
        ];
        
        $this->view('home/index', $data);
    }
}