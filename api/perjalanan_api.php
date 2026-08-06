<?php
/**
 * Perjalanan API Endpoint
 * Fishing Log Application - OOP Version
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/Database.php';
startSecureSession();

header('Content-Type: application/json; charset=utf-8');
setCorsHeaders();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if (!isset($_SESSION['id_pengguna'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $db = new Database();
    $perjalanan = new Perjalanan($db);
    $user_id = $_SESSION['id_pengguna'];
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    if ($method !== 'GET') { requireCsrfToken(); }

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $data = $perjalanan->getById($id, $user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            $data = $perjalanan->getAll($user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        }
        exit();
    }

    if ($method === 'POST') {
        $waktu_mulai = $input['waktu_mulai'] ?? null;
        $waktu_selesai = $input['waktu_selesai'] ?? null;
        $jarak = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : null;

        if (!$waktu_mulai || !$waktu_selesai || $jarak === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $result = $perjalanan->create($user_id, $waktu_mulai, $waktu_selesai, $jarak);
        echo json_encode($result);
        exit();
    }

    if ($method === 'PUT') {
        $id = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        $waktu_mulai = $input['waktu_mulai'] ?? null;
        $waktu_selesai = $input['waktu_selesai'] ?? null;
        $jarak = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : null;

        if (!$id || !$waktu_mulai || !$waktu_selesai || $jarak === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $result = $perjalanan->update($id, $user_id, $waktu_mulai, $waktu_selesai, $jarak);
        echo json_encode($result);
        exit();
    }

    if ($method === 'DELETE') {
        $id = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }

        $result = $perjalanan->delete($id, $user_id);
        echo json_encode($result);
        exit();
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Throwable $e) {
    error_log($e->getMessage());
    apiErrorResponse();
}
