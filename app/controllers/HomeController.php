<?php
require_once 'app/core/Controller.php';

class HomeController extends Controller {
    
    public function index() {
        // Convert BUSINESS_DAYS string to array for processing
        $businessDaysArray = explode(',', BUSINESS_DAYS);
        $businessDaysSpanish = [];
        
        // Translate days to Spanish
        $dayTranslations = [
            'monday' => 'Lunes',
            'tuesday' => 'Martes', 
            'wednesday' => 'Miércoles',
            'thursday' => 'Jueves',
            'friday' => 'Viernes',
            'saturday' => 'Sábado',
            'sunday' => 'Domingo'
        ];
        
        foreach ($businessDaysArray as $day) {
            $day = trim($day);
            if (isset($dayTranslations[$day])) {
                $businessDaysSpanish[] = $dayTranslations[$day];
            }
        }
        
        $data = [
            'title' => 'Bienvenido a ' . APP_NAME,
            'businessHours' => [
                'start' => substr(BUSINESS_HOURS_START, 0, 2), // Extract hour from HH:MM format
                'end' => substr(BUSINESS_HOURS_END, 0, 2),     // Extract hour from HH:MM format
                'days' => implode(', ', array_slice($businessDaysSpanish, 0, -1)) . ' y ' . end($businessDaysSpanish)
            ]
        ];
        
        $this->view('home/index', $data);
    }
}