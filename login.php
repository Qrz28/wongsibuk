<?php
/**
 * Login API Endpoint
 * Fishing Log Application
 */

// Start session FIRST before any headers
session_start();

// Include config to get CORS function
if (!file_exists('config.php')) {
    http_response_code(500);
    die(json_encode([
        'success' => false,
        'message' => 'File config.php tidak ditemukan',
        'debug' => 'Current dir: ' . __DIR__
    ]));
}

require_once 'config.php';

header('Content-Type: application/json; charset=utf-8');
setCorsHeaders();

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Ensure it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
    exit();
}

// Check if database is connected
if (!$conn || $conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database connection error: ' . ($conn->connect_error ?? 'Unknown'),
        'debug' => true
    ]);
    exit();
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['email']) || !isset($input['password'])) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email dan password harus diisi'
    ]);
    exit();
}

$email = trim($input['email']);
$password = trim($input['password']);
$remember = isset($input['remember']) ? (bool)$input['remember'] : false;

// Basic validation
if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email dan password tidak boleh kosong'
    ]);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Format email tidak valid'
    ]);
    exit();
}

// Validate password length
if (strlen($password) < 6) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Kata sandi minimal 6 karakter'
    ]);
    exit();
}

// Query database
$query = "SELECT id_pengguna, nama, email, password FROM pengguna WHERE email = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $conn->error,
        'debug' => [
            'error' => $conn->error,
            'errno' => $conn->errno
        ]
    ]);
    exit();
}

$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Email atau password salah'
    ]);
    exit();
}

$user = $result->fetch_assoc();

// Verify password using bcrypt
if (!password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Email atau password salah'
    ]);
    exit();
}

// Set session variables
$_SESSION['id_pengguna'] = $user['id_pengguna'];
$_SESSION['nama'] = $user['nama'];
$_SESSION['email'] = $user['email'];
$_SESSION['login_time'] = date('Y-m-d H:i:s');

// If remember me is checked, set cookie for 30 days
if ($remember) {
    $cookie_name = 'fishing_log_user';
    $cookie_value = base64_encode($user['id_pengguna'] . ':' . $user['email']);
    $cookie_expire = time() + (30 * 24 * 60 * 60); // 30 days
    setcookie($cookie_name, $cookie_value, $cookie_expire, '/', '', false, true);
}

// Success response
http_response_code(200);
echo json_encode([
    'success' => true,
    'message' => 'Login berhasil',
    'data' => [
        'id_pengguna' => $user['id_pengguna'],
        'nama' => $user['nama'],
        'email' => $user['email']
    ],
    'redirect' => 'dashboard.php'
]);

$stmt->close();
$conn->close();
?>
