<?php
/**
 * Catatan Memancing API Endpoint
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
    $catatan = new Catatan($db);
    $user_id = $_SESSION['id_pengguna'];
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    if ($method !== 'GET') { requireCsrfToken(); }

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $data = $catatan->getById($id, $user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            $data = $catatan->getAll($user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        }
        exit();
    }

    if ($method === 'POST') {
        $id_perjalanan = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        $id_spot = isset($input['id_spot']) ? (int)$input['id_spot'] : null;
        $catatan_text = trim($input['catatan'] ?? '');

        if (!$id_perjalanan || !$id_spot) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $result = $catatan->create($id_perjalanan, $id_spot, $catatan_text, $user_id);
        echo json_encode($result);
        exit();
    }

    if ($method === 'PUT') {
        $id = isset($input['id_catatan']) ? (int)$input['id_catatan'] : null;
        $id_perjalanan = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        $id_spot = isset($input['id_spot']) ? (int)$input['id_spot'] : null;
        $catatan_text = trim($input['catatan'] ?? '');

        if (!$id || !$id_perjalanan || !$id_spot) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $result = $catatan->update($id, $id_perjalanan, $id_spot, $catatan_text, $user_id);
        echo json_encode($result);
        exit();
    }

    if ($method === 'DELETE') {
        $id = isset($input['id_catatan']) ? (int)$input['id_catatan'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }

        $result = $catatan->delete($id, $user_id);
        echo json_encode($result);
        exit();
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Throwable $e) {
    error_log($e->getMessage());
    apiErrorResponse();
}
