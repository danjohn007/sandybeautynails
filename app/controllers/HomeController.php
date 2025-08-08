<?php
require_once 'app/core/Controller.php';

class HomeController extends Controller {
    
    public function index() {
        $data = [
            'title' => 'Bienvenido a ' . APP_NAME,
            'businessHours' => [
                'start' => BUSINESS_START_HOUR,
                'end' => BUSINESS_END_HOUR,
                'days' => implode(', ', array_slice(BUSINESS_DAYS, 0, -1)) . ' y ' . end(BUSINESS_DAYS)
            ]
        ];
        
        $this->view('home/index', $data);
    }
}