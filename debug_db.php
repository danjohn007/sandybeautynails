<?php
// Debug database connectivity
session_start();
require_once 'config/config.php';

echo "<h2>Database Connection Test</h2>";
echo "<p><strong>Configuration:</strong></p>";
echo "<ul>";
echo "<li>DB_HOST: " . DB_HOST . "</li>";
echo "<li>DB_NAME: " . DB_NAME . "</li>";
echo "<li>DB_USER: " . DB_USER . "</li>";
echo "<li>DB_CHARSET: " . DB_CHARSET . "</li>";
echo "</ul>";

try {
    // Test connection using configured settings
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    echo "<p><strong>Attempting MySQL connection with DSN:</strong> $dsn</p>";
    
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    
    echo "<p style='color: green;'><strong>✓ MySQL Connection Successful!</strong></p>";
    
    // Test tables
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p><strong>Available tables:</strong></p>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>$table</li>";
    }
    echo "</ul>";
    
    // Test customers table
    if (in_array('customers', $tables)) {
        $count = $pdo->query("SELECT COUNT(*) FROM customers")->fetchColumn();
        echo "<p><strong>Customers count:</strong> $count</p>";
    } else {
        echo "<p style='color: red;'><strong>⚠ Missing customers table</strong></p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'><strong>✗ MySQL Connection Failed:</strong></p>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    
    // Try alternative SQLite connection
    echo "<hr>";
    echo "<h3>Attempting SQLite fallback...</h3>";
    
    try {
        $sqliteDb = __DIR__ . '/storage/sandy_beauty_nails.db';
        echo "<p><strong>SQLite path:</strong> $sqliteDb</p>";
        
        $pdo = new PDO("sqlite:$sqliteDb");
        echo "<p style='color: green;'><strong>✓ SQLite Connection Successful!</strong></p>";
        
        $tables = $pdo->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
        echo "<p><strong>Available tables:</strong></p>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'><strong>✗ SQLite Connection Also Failed:</strong></p>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }
}

echo "<hr>";
echo "<p><a href='/booking'>Test Booking Page</a> | <a href='/demo.php?route=booking'>Demo Booking Page</a></p>";
?>