<?php
require_once 'app/core/Controller.php';

class HomeController extends Controller {
    
    public function index() {
        // Use BUSINESS_DAYS directly as it's already an array
        $businessDaysArray = BUSINESS_DAYS;
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
            $day = trim(strtolower($day));
            if (isset($dayTranslations[$day])) {
                $businessDaysSpanish[] = $dayTranslations[$day];
            }
        }
        
        $data = [
            'title' => 'Bienvenido a ' . APP_NAME,
            'businessHours' => [
                'start' => BUSINESS_START_HOUR,
                'end' => BUSINESS_END_HOUR,
                'days' => implode(', ', array_slice($businessDaysSpanish, 0, -1)) . ' y ' . end($businessDaysSpanish)
            ]
        ];
        
        $this->view('home/index', $data);
    }
}