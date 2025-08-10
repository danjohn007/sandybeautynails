<?php
// Database configuration with production fallback
define('DB_HOST', 'localhost');
define('DB_NAME', 'fix360_sandy');
define('DB_USER', 'fix360_sandy');
define('DB_PASS', 'Danjohn007');
define('DB_CHARSET', 'utf8mb4');

// App configuration
define('APP_URL', 'https://fix360.app/sandy/');
define('APP_NAME', 'Sandy Beauty Nails');
define('APP_TIMEZONE', 'America/Mexico_City');

// Business hours
define('BUSINESS_START_HOUR', 8);
define('BUSINESS_END_HOUR', 19);
define('BUSINESS_DAYS', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
define('APPOINTMENT_DURATION', 60); // minutes

// Mercado Pago configuration
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

// Error reporting (reduced for production)
error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Database fallback flag - production will attempt MySQL first, then SQLite
define('ENABLE_DB_FALLBACK', true);
?>