<?php
// Demo configuration - uses SQLite for demonstration
define('DB_HOST', 'sqlite');
define('DB_NAME', __DIR__ . '/storage/sandy_beauty_nails.db');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// App configuration
define('APP_URL', 'http://localhost:8080');
define('APP_NAME', 'Sandy Beauty Nails');
define('APP_TIMEZONE', 'America/Mexico_City');

// Business hours
define('BUSINESS_START_HOUR', 8);
define('BUSINESS_END_HOUR', 19);
define('BUSINESS_DAYS', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
define('APPOINTMENT_DURATION', 60); // minutes

// Mercado Pago configuration - Demo mode
define('MP_ACCESS_TOKEN', ''); // Configure with your Mercado Pago access token
define('MP_PUBLIC_KEY', ''); // Configure with your Mercado Pago public key

// Security
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_EXPIRE', 1800); // 30 minutes

// Email configuration (optional)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('EMAIL_FROM', 'noreply@sandybeautynails.com');

// Set timezone
date_default_timezone_set(APP_TIMEZONE);

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Demo mode flag
define('DEMO_MODE', true);