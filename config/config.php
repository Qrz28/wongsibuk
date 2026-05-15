<?php
/**
 * Configuration File
 * Fishing Log Application - OOP Version
 */

// Detect environment
$is_localhost = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

// Database credentials
if ($is_localhost) {
    // Localhost configuration
    define('DB_HOST', 'localhost');
    define('DB_USER', 'root');
    define('DB_PASS', '');
    define('DB_NAME', 'fishinglog');
} else {
    // Production configuration
    define('DB_HOST', 'sql304.infinityfree.com');
    define('DB_USER', 'userif0_41317257');
    define('DB_PASS', 'MVTrL6n7J1o0');
    define('DB_NAME', 'iifi_4131257_fishing');
}

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
