<?php
/**
 * Sandy Beauty Nails - Configuration File
 * Database and application configuration settings
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'fix360_sandy');
define('DB_USER', 'fix360_sandy');
define('DB_PASS', 'Danjon007');
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_URL', 'https://fix360.app/sandy/');
define('APP_NAME', 'Sandy Beauty Nails');
define('APP_VERSION', '1.0.0');

// Demo Mode Configuration
define('DEMO_MODE', false);

// Security Configuration
define('ENCRYPTION_KEY', 'your-secret-encryption-key-here');
define('SESSION_TIMEOUT', 3600); // 1 hour

// Timezone Configuration
define('APP_TIMEZONE', 'America/Mexico_City');

// Email Configuration (if needed)
define('SMTP_HOST', '');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_ENCRYPTION', 'tls');

// Payment Configuration (Mercado Pago)
define('MP_ACCESS_TOKEN', '');
define('MP_PUBLIC_KEY', '');
define('MP_SANDBOX_MODE', false);

// File Upload Configuration
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_FILE_TYPES', 'jpg,jpeg,png,gif,pdf');

// Business Configuration
define('BUSINESS_HOURS_START', '08:00');
define('BUSINESS_HOURS_END', '19:00');
define('BUSINESS_DAYS', 'monday,tuesday,wednesday,thursday,friday,saturday');

?>