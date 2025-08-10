<?php
// Test production booking scenario
session_start();
require_once 'config/config.php'; // Use production config (MySQL)

echo "<h2>Production Database Test</h2>";

try {
    require_once 'app/core/Database.php';
    require_once 'app/core/Controller.php';
    require_once 'app/models/Customer.php';
    require_once 'app/controllers/BookingController.php';
    
    echo "<p>Attempting to create BookingController...</p>";
    
    $controller = new BookingController();
    echo "<p style='color: green;'>✓ BookingController created successfully</p>";
    
    // Test customer check with fallback
    $_POST['phone'] = '555-1001';
    
    echo "<p>Testing customer check...</p>";
    
    // Capture output
    ob_start();
    $controller->checkCustomer();
    $output = ob_get_clean();
    
    $response = json_decode($output, true);
    if ($response && isset($response['exists'])) {
        echo "<p style='color: green;'>✓ Customer check successful: " . ($response['exists'] ? 'Existing customer found' : 'New customer') . "</p>";
        if ($response['exists']) {
            echo "<p><strong>Customer:</strong> " . $response['customer']['name'] . "</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ Unexpected response: " . htmlspecialchars($output) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>This demonstrates the production database issue.</p>";
}

echo "<hr>";
echo "<p><a href='/debug_db.php'>Back to Database Debug</a> | <a href='/demo.php?route=booking'>Demo Booking</a></p>";
?>