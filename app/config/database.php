<?php
// Database configuration
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'fix360_sandy');
    define('DB_USER', 'fix360_sandy');
    define('DB_PASS', 'Danjohn007');
    define('DB_CHARSET', 'utf8mb4');
}

// Database connection settings
define('DB_DRIVER', 'mysql');
define('DB_PORT', 3306);
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . DB_CHARSET . ' COLLATE utf8mb4_unicode_ci'
]);