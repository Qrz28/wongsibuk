<?php
/**
 * Spot Memancing API Endpoint
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

if (!isset($_SESSION['id_pengguna'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

try {
    $db = new Database();
    $spot = new Spot($db);
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $data = $spot->getById($id);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            $data = $spot->getAll();
            echo json_encode(['success' => true, 'data' => $data]);
        }
        exit();
    }

    if ($method === 'POST') {
        $alamat = trim($input['alamat'] ?? '');
        $deskripsi_spot = trim($input['deskripsi_spot'] ?? '');
        $jenis_spot = trim($input['jenis_spot'] ?? '');
        $jarak_lokasi = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : 0;

        if (empty($alamat) || empty($deskripsi_spot) || empty($jenis_spot)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        if (strlen($alamat) < 3) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Alamat minimal 3 karakter']);
            exit();
        }

        if (strlen($deskripsi_spot) < 10) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Deskripsi minimal 10 karakter']);
            exit();
        }

        $result = $spot->create($alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi);
        if ($result['success']) {
            http_response_code(201);
            $result['message'] = 'Spot memancing berhasil ditambahkan';
        }
        echo json_encode($result);
        exit();
    }

    if ($method === 'PUT') {
        $id = isset($input['id_spot']) ? (int)$input['id_spot'] : null;
        $alamat = trim($input['alamat'] ?? '');
        $deskripsi_spot = trim($input['deskripsi_spot'] ?? '');
        $jenis_spot = trim($input['jenis_spot'] ?? '');
        $jarak_lokasi = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : 0;

        if (!$id || empty($alamat) || empty($deskripsi_spot) || empty($jenis_spot)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        if (strlen($alamat) < 3) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Alamat minimal 3 karakter']);
            exit();
        }

        if (strlen($deskripsi_spot) < 10) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Deskripsi minimal 10 karakter']);
            exit();
        }

        if (!$spot->exists($id)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Spot tidak ditemukan']);
            exit();
        }

        $result = $spot->update($id, $alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi);
        if ($result['success']) {
            $result['message'] = 'Spot memancing berhasil diperbarui';
        }
        echo json_encode($result);
        exit();
    }

    if ($method === 'DELETE') {
        $id = isset($input['id_spot']) ? (int)$input['id_spot'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID spot harus diberikan']);
            exit();
        }

        if (!$spot->exists($id)) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Spot tidak ditemukan']);
            exit();
        }

        $result = $spot->delete($id);
        if ($result['success']) {
            $result['message'] = 'Spot memancing berhasil dihapus';
        }
        echo json_encode($result);
        exit();
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
