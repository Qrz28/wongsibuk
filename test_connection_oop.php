<?php
/**
 * Test Connection - OOP Version
 */

require_once 'config/config.php';
require_once 'config/Database.php';

echo "Testing Database Connection (OOP)...\n\n";

try {
    $db = new Database();
    $conn = $db->getConnection();
    echo "✓ Database connection successful!\n";
    echo "✓ Connected to: " . DB_NAME . "\n";
    $db->close();
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
}

echo "\nTesting Class Autoload...\n\n";

$classes_to_test = ['Database', 'User', 'Perjalanan', 'Spot', 'Catatan', 'Tangkapan', 'Foto'];

foreach ($classes_to_test as $class) {
    if (class_exists($class)) {
        echo "✓ Class $class loaded successfully\n";
    } else {
        echo "✗ Class $class not found\n";
    }
}

echo "\nDone!\n";
