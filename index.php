<?php
session_start();
require_once 'config/config.php';
require_once 'app/core/Database.php';
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

// Admin routes
$router->add('admin', 'AdminController@login');
$router->add('admin/login', 'AdminController@login');
$router->add('admin/authenticate', 'AdminController@authenticate');
$router->add('admin/logout', 'AdminController@logout');
$router->add('admin/dashboard', 'AdminController@dashboard');
$router->add('admin/reservations', 'AdminController@reservations');
$router->add('admin/clients', 'AdminController@clients');
$router->add('admin/finances', 'AdminController@finances');
$router->add('admin/reports', 'AdminController@reports');
$router->add('admin/update-status', 'AdminController@updateStatus');

// Payment routes
$router->add('payment/create', 'PaymentController@create');
$router->add('payment/webhook', 'PaymentController@webhook');
$router->add('payment/success', 'PaymentController@success');
$router->add('payment/failure', 'PaymentController@failure');

// Get the requested route
$route = $_GET['route'] ?? '';

try {
    $router->dispatch($route);
} catch (Exception $e) {
    http_response_code(404);
    include 'app/views/errors/404.php';
}