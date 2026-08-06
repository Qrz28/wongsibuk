<?php
/**
 * Session Check API Endpoint
 * Fishing Log Application
 */

require_once 'config.php';
startSecureSession();
header('Content-Type: application/json; charset=utf-8');
setCorsHeaders();

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if user is logged in
if (isset($_SESSION['id_pengguna'])) {
    echo json_encode([
        'success' => true,
        'logged_in' => true,
        'data' => [
            'id_pengguna' => $_SESSION['id_pengguna'],
            'nama' => $_SESSION['nama'],
            'email' => $_SESSION['email'],
            'login_time' => $_SESSION['login_time']
        ]
    ]);
} else {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'logged_in' => false,
        'message' => 'Anda tidak login'
    ]);
}
?>
