<?php
/**
 * Local Configuration File
 * Fishing Log Application - OOP Version
 * Untuk development di localhost
 */

// Database credentials - LOCALHOST
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'wongsibuk');

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Autoload classes
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/../classes/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

/**
 * CORS Configuration
 * Restrict to localhost for security
 */
function setCorsHeaders() {
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    // Allow only localhost origins
    if (strpos($origin, 'localhost') !== false || strpos($origin, '127.0.0.1') !== false) {
        header('Access-Control-Allow-Origin: ' . $origin);
    }
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}
