<?php
/**
 * Tangkapan API Endpoint
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
    $tangkapan = new Tangkapan($db);
    $user_id = $_SESSION['id_pengguna'];
    $method = $_SERVER['REQUEST_METHOD'];
    $input = json_decode(file_get_contents('php://input'), true) ?? [];

    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $data = $tangkapan->getById($id, $user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            $data = $tangkapan->getAll($user_id);
            echo json_encode(['success' => true, 'data' => $data]);
        }
        exit();
    }

    if ($method === 'POST') {
        $id_catatan = isset($input['id_catatan']) ? (int)$input['id_catatan'] : null;
        $jenis_ikan = trim($input['jenis_ikan'] ?? '');
        $nama_ikan = trim($input['nama_ikan'] ?? '');
        $jumlah_ikan = isset($input['jumlah_ikan']) ? (int)$input['jumlah_ikan'] : null;
        $tanggal_jawa = trim($input['tanggal_jawa'] ?? '');

        if (!$id_catatan || !$jenis_ikan || !$nama_ikan || $jumlah_ikan === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $result = $tangkapan->create($id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa, $user_id);
        echo json_encode($result);
        exit();
    }

    if ($method === 'PUT') {
        $id = isset($input['id_tangkapan']) ? (int)$input['id_tangkapan'] : null;
        $id_catatan = isset($input['id_catatan']) ? (int)$input['id_catatan'] : null;
        $jenis_ikan = trim($input['jenis_ikan'] ?? '');
        $nama_ikan = trim($input['nama_ikan'] ?? '');
        $jumlah_ikan = isset($input['jumlah_ikan']) ? (int)$input['jumlah_ikan'] : null;
        $tanggal_jawa = trim($input['tanggal_jawa'] ?? '');

        if (!$id || !$id_catatan || !$jenis_ikan || !$nama_ikan || $jumlah_ikan === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $result = $tangkapan->update($id, $id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa, $user_id);
        echo json_encode($result);
        exit();
    }

    if ($method === 'DELETE') {
        $id = isset($input['id_tangkapan']) ? (int)$input['id_tangkapan'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }

        $result = $tangkapan->delete($id, $user_id);
        echo json_encode($result);
        exit();
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
