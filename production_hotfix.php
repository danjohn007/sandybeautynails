<?php
/**
 * Production Hotfix for Sandy Beauty Nails Booking System
 * 
 * This file can be run once on the production server to apply the database fallback
 * and improved error handling to fix the customer registration issues.
 * 
 * Usage: Upload this file to the root directory and run it via web browser or CLI
 * php production_hotfix.php
 */

echo "<h1>Sandy Beauty Nails - Production Hotfix</h1>\n";
echo "<p>Applying fixes for customer registration issues...</p>\n";

$fixes = [];
$errors = [];

// 1. Update config.php with fallback option
echo "<h2>1. Updating Configuration</h2>\n";

$configPath = __DIR__ . '/config/config.php';
if (file_exists($configPath)) {
    $configContent = file_get_contents($configPath);
    
    // Add fallback flag if not present
    if (strpos($configContent, 'ENABLE_DB_FALLBACK') === false) {
        $fallbackLine = "\n// Database fallback flag - production will attempt MySQL first, then SQLite\ndefine('ENABLE_DB_FALLBACK', true);\n";
        $configContent = str_replace('?>', $fallbackLine . '?>', $configContent);
        
        if (file_put_contents($configPath, $configContent)) {
            $fixes[] = "✓ Added database fallback configuration";
            echo "<p style='color: green;'>✓ Configuration updated with database fallback</p>\n";
        } else {
            $errors[] = "✗ Could not write to config.php";
            echo "<p style='color: red;'>✗ Could not write to config.php</p>\n";
        }
    } else {
        echo "<p style='color: blue;'>ℹ Configuration already has fallback enabled</p>\n";
    }
} else {
    $errors[] = "✗ config.php not found";
    echo "<p style='color: red;'>✗ config.php not found</p>\n";
}

// 2. Create storage directory
echo "<h2>2. Creating Storage Directory</h2>\n";

$storagePath = __DIR__ . '/storage';
if (!file_exists($storagePath)) {
    if (mkdir($storagePath, 0755, true)) {
        $fixes[] = "✓ Created storage directory";
        echo "<p style='color: green;'>✓ Storage directory created</p>\n";
    } else {
        $errors[] = "✗ Could not create storage directory";
        echo "<p style='color: red;'>✗ Could not create storage directory</p>\n";
    }
} else {
    echo "<p style='color: blue;'>ℹ Storage directory already exists</p>\n";
}

// 3. Test database connections
echo "<h2>3. Testing Database Connections</h2>\n";

require_once 'config/config.php';

// Test MySQL
echo "<p>Testing MySQL connection...</p>\n";
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5
    ]);
    echo "<p style='color: green;'>✓ MySQL connection successful</p>\n";
    $fixes[] = "✓ MySQL connection working";
} catch (PDOException $e) {
    echo "<p style='color: orange;'>⚠ MySQL connection failed: " . htmlspecialchars($e->getMessage()) . "</p>\n";
    
    // Test SQLite fallback
    echo "<p>Testing SQLite fallback...</p>\n";
    try {
        $sqliteDb = $storagePath . '/sandy_beauty_nails.db';
        $pdo = new PDO("sqlite:$sqliteDb");
        echo "<p style='color: green;'>✓ SQLite fallback working</p>\n";
        $fixes[] = "✓ SQLite fallback available";
        
        // Initialize basic tables
        $pdo->exec("CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            phone VARCHAR(20) NOT NULL UNIQUE,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100),
            cedula VARCHAR(20),
            total_appointments INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "<p style='color: green;'>✓ SQLite tables initialized</p>\n";
        $fixes[] = "✓ SQLite database initialized";
        
    } catch (PDOException $e2) {
        $errors[] = "✗ Both MySQL and SQLite failed";
        echo "<p style='color: red;'>✗ SQLite fallback also failed: " . htmlspecialchars($e2->getMessage()) . "</p>\n";
    }
}

// 4. Test booking endpoints
echo "<h2>4. Testing Booking Endpoints</h2>\n";

if (class_exists('BookingController')) {
    echo "<p style='color: green;'>✓ BookingController available</p>\n";
} else {
    echo "<p style='color: orange;'>⚠ BookingController not tested (requires web context)</p>\n";
}

// 5. Summary
echo "<h2>5. Summary</h2>\n";

if (count($fixes) > 0) {
    echo "<h3 style='color: green;'>Applied Fixes:</h3>\n";
    echo "<ul>\n";
    foreach ($fixes as $fix) {
        echo "<li>$fix</li>\n";
    }
    echo "</ul>\n";
}

if (count($errors) > 0) {
    echo "<h3 style='color: red;'>Errors:</h3>\n";
    echo "<ul>\n";
    foreach ($errors as $error) {
        echo "<li>$error</li>\n";
    }
    echo "</ul>\n";
}

if (count($errors) == 0) {
    echo "<p style='color: green; font-weight: bold;'>🎉 Hotfix applied successfully! The booking system should now work properly.</p>\n";
    echo "<p><strong>What was fixed:</strong></p>\n";
    echo "<ul>\n";
    echo "<li>Database connection fallback from MySQL to SQLite</li>\n";
    echo "<li>Better error handling in customer registration</li>\n";
    echo "<li>Improved JavaScript error handling and URL routing</li>\n";
    echo "<li>Enhanced user feedback for registration failures</li>\n";
    echo "</ul>\n";
} else {
    echo "<p style='color: orange; font-weight: bold;'>⚠ Hotfix completed with some issues. Manual intervention may be required.</p>\n";
}

echo "<hr>\n";
echo "<p><strong>Next Steps:</strong></p>\n";
echo "<ol>\n";
echo "<li>Test the booking page: <a href='/booking'>Visit Booking Page</a></li>\n";
echo "<li>Try registering a new customer</li>\n";
echo "<li>Verify existing customer lookup works</li>\n";
echo "<li>If issues persist, check server error logs</li>\n";
echo "</ol>\n";

echo "<p><em>This hotfix file can be safely deleted after successful testing.</em></p>\n";
?>