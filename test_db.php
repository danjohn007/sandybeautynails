<?php
// Simple test to check SQLite connection
try {
    $dbPath = __DIR__ . '/storage/sandy_beauty_nails.db';
    echo "Database path: $dbPath\n";
    
    if (!file_exists(dirname($dbPath))) {
        mkdir(dirname($dbPath), 0755, true);
        echo "Created directory: " . dirname($dbPath) . "\n";
    }
    
    $pdo = new PDO("sqlite:$dbPath");
    echo "SQLite connection successful!\n";
    
    // Test simple query
    $pdo->exec("CREATE TABLE IF NOT EXISTS test (id INTEGER PRIMARY KEY, name TEXT)");
    echo "Table creation successful!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test current working directory
echo "Current directory: " . getcwd() . "\n";
echo "File exists: " . (file_exists($dbPath) ? 'Yes' : 'No') . "\n";
echo "Directory writable: " . (is_writable(dirname($dbPath)) ? 'Yes' : 'No') . "\n";
?>