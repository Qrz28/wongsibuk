<?php
/**
 * Test User API - Debugging
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing User API...</h2>";
echo "<pre>";

// Test 1: Load config
echo "1. Loading config...\n";
try {
    require_once __DIR__ . '/../config/config.php';
    echo "✓ Config loaded\n";
    echo "  DB_HOST: " . DB_HOST . "\n";
    echo "  DB_USER: " . DB_USER . "\n";
    echo "  DB_NAME: " . DB_NAME . "\n";
} catch (Exception $e) {
    echo "✗ Config error: " . $e->getMessage() . "\n";
    exit();
}

// Test 2: Load Database class
echo "\n2. Loading Database class...\n";
try {
    require_once __DIR__ . '/../config/Database.php';
    echo "✓ Database class loaded\n";
} catch (Exception $e) {
    echo "✗ Database class error: " . $e->getMessage() . "\n";
    exit();
}

// Test 3: Autoload classes
echo "\n3. Testing autoload...\n";
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
        echo "✓ Autoloaded: $class_name\n";
    } else {
        echo "✗ Class not found: $class_name ($file)\n";
    }
});

// Test 4: Try to instantiate Database
echo "\n4. Testing Database connection...\n";
try {
    $db = new Database();
    echo "✓ Database object created\n";
    $conn = $db->getConnection();
    echo "✓ Database connection successful\n";
    $db->close();
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit();
}

// Test 5: Try to instantiate User
echo "\n5. Testing User class...\n";
try {
    $db = new Database();
    $user = new User($db);
    echo "✓ User object created\n";
    $db->close();
} catch (Exception $e) {
    echo "✗ User class error: " . $e->getMessage() . "\n";
    exit();
}

echo "\n✓ All tests passed!</pre>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Buat database 'wongsibuk' di phpMyAdmin</li>";
echo "<li>Import SQL dari database_setup.sql</li>";
echo "<li>Coba login di <a href='../views/login.html'>login page</a></li>";
echo "</ol>";
