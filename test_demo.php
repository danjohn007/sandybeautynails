<?php
session_start();

// Always use demo configuration for testing
require_once 'config/temp_config.php';

// Handle database class loading for demo
if (!class_exists('Database')) {
    require_once 'app/core/DemoDatabase.php';
    class_alias('DemoDatabase', 'Database');
}

require_once 'app/core/Router.php';
require_once 'app/core/Controller.php';

// Initialize router
$router = new Router();

// Public routes
$router->add('', 'HomeController@index');
$router->add('booking', 'BookingController@index');
$router->add('booking/submit', 'BookingController@submit');
$router->add('booking/check-customer', 'BookingController@checkCustomer');
$router->add('booking/get-availability', 'BookingController@getAvailability');
$router->add('booking/success', 'BookingController@success');

// Get the requested route
$route = $_GET['route'] ?? '';

try {
    $router->dispatch($route);
} catch (Exception $e) {
    http_response_code(404);
    include 'app/views/errors/404.php';
}