<?php
/**
 * User API Endpoint
 * Fishing Log Application - OOP Version
 */

session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';

header('Content-Type: application/json; charset=utf-8');
setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $db = new Database();
    $user = new User($db);
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    if ($method === 'POST') {
        // Login or Register
        $action = $input['action'] ?? '';

        if ($action === 'register') {
            $nama = trim($input['nama'] ?? '');
            $email = trim($input['email'] ?? '');
            $password = trim($input['password'] ?? '');
            $confirm_password = trim($input['confirm_password'] ?? '');

            // Validation
            if (empty($nama) || empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nama, email, dan password tidak boleh kosong']);
                exit();
            }

            if (strlen($nama) < 3) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Nama minimal 3 karakter']);
                exit();
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
                exit();
            }

            if (strlen($password) < 6) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Kata sandi minimal 6 karakter']);
                exit();
            }

            if ($password !== $confirm_password) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Konfirmasi kata sandi tidak cocok']);
                exit();
            }

            $result = $user->register($nama, $email, $password);

            if ($result['success']) {
                $_SESSION['id_pengguna'] = $result['id_pengguna'];
                $_SESSION['nama'] = $result['nama'];
                $_SESSION['email'] = $result['email'];
                $_SESSION['login_time'] = date('Y-m-d H:i:s');

                http_response_code(201);
                echo json_encode([
                    'success' => true,
                    'message' => 'Pendaftaran berhasil!',
                    'data' => $result,
                    'redirect' => 'dashboard.php'
                ]);
            } else {
                http_response_code(409);
                echo json_encode($result);
            }
        } elseif ($action === 'login') {
            $email = trim($input['email'] ?? '');
            $password = trim($input['password'] ?? '');

            if (empty($email) || empty($password)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Email dan password harus diisi']);
                exit();
            }

            $result = $user->login($email, $password);

            if ($result['success']) {
                $_SESSION['id_pengguna'] = $result['id_pengguna'];
                $_SESSION['nama'] = $result['nama'];
                $_SESSION['email'] = $result['email'];
                $_SESSION['login_time'] = date('Y-m-d H:i:s');

                echo json_encode([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'data' => $result,
                    'redirect' => 'dashboard.php'
                ]);
            } else {
                http_response_code(401);
                echo json_encode($result);
            }
        } else {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Action tidak valid']);
        }
        exit();
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
