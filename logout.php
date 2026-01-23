<?php
/**
 * Logout API Endpoint
 * Fishing Log Application
 */

header('Content-Type: application/json; charset=utf-8');

// Start session
session_start();

// Destroy session
session_destroy();

// Delete remember me cookie if exists
if (isset($_COOKIE['fishing_log_user'])) {
    setcookie('fishing_log_user', '', time() - 3600, '/');
}

echo json_encode([
    'success' => true,
    'message' => 'Logout berhasil',
    'redirect' => 'login.html'
]);
?>
