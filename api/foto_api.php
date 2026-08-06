<?php
/**
 * Foto API Endpoint
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
    $foto = new Foto($db);
    $user_id = $_SESSION['id_pengguna'];
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    if ($method !== 'GET') { requireCsrfToken(); }

    if ($method === 'GET') {
        if (isset($_GET['id_tangkapan'])) {
            $id_tangkapan = (int)$_GET['id_tangkapan'];
            $result = $foto->getByTangkapan($id_tangkapan, $user_id);
            if (isset($result['success']) && !$result['success']) {
                http_response_code(403);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => true, 'data' => $result]);
            }
        } elseif (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $data = $foto->getById($id, $user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            $data = $foto->getAll($user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        }
        exit();
    }

    if ($method === 'POST') {
        $id_tangkapan = isset($_POST['id_tangkapan']) ? (int)$_POST['id_tangkapan'] : null;
        $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';

        if (!$id_tangkapan) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tangkapan diperlukan']);
            exit();
        }

        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File foto diperlukan']);
            exit();
        }

        $file = $_FILES['foto'];
        $result = $foto->upload($id_tangkapan, $file, $deskripsi, $user_id);
        echo json_encode($result);
        exit();
    }

    if ($method === 'DELETE') {
        $id = isset($input['id_foto']) ? (int)$input['id_foto'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }

        $result = $foto->delete($id, $user_id);
        echo json_encode($result);
        exit();
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Throwable $e) {
    error_log($e->getMessage());
    apiErrorResponse();
}
