<?php
/**
 * CRUD API untuk tabel `tangkapan`
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
session_start();

if (!isset($_SESSION['id_pengguna'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['id_pengguna'];
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $query = "SELECT t.id_tangkapan, t.id_perjalanan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                      FROM tangkapan t
                      JOIN perjalanan p ON t.id_perjalanan = p.id_perjalanan
                      WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $id, $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
            $stmt->close();
            exit();
        }

        $query = "SELECT t.id_tangkapan, t.id_perjalanan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                  FROM tangkapan t
                  JOIN perjalanan p ON t.id_perjalanan = p.id_perjalanan
                  WHERE p.id_pengguna = ?
                  ORDER BY t.id_tangkapan DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $rows = [];
        while ($row = $res->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $rows]);
        $stmt->close();
        exit();
    }

    if ($method === 'POST') {
        $id_perjalanan = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        $jenis_ikan = trim($input['jenis_ikan'] ?? '');
        $nama_ikan = trim($input['nama_ikan'] ?? '');
        $jumlah_ikan = isset($input['jumlah_ikan']) ? (int)$input['jumlah_ikan'] : null;
        $tanggal_jawa = trim($input['tanggal_jawa'] ?? '');

        if (!$id_perjalanan || !$jenis_ikan || !$nama_ikan || $jumlah_ikan === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        // verify perjalanan belongs to user
        $q = "SELECT id_perjalanan FROM perjalanan WHERE id_perjalanan = ? AND id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id_perjalanan, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Perjalanan tidak valid']);
            $s->close();
            exit();
        }
        $s->close();

        $query = "INSERT INTO tangkapan (id_perjalanan, jenis_ikan, nama_ikan, jumlah_ikan, tanggal_jawa) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issis', $id_perjalanan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    if ($method === 'PUT') {
        $id = isset($input['id_tangkapan']) ? (int)$input['id_tangkapan'] : null;
        $id_perjalanan = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        $jenis_ikan = trim($input['jenis_ikan'] ?? '');
        $nama_ikan = trim($input['nama_ikan'] ?? '');
        $jumlah_ikan = isset($input['jumlah_ikan']) ? (int)$input['jumlah_ikan'] : null;
        $tanggal_jawa = trim($input['tanggal_jawa'] ?? '');

        if (!$id || !$id_perjalanan || !$jenis_ikan || !$nama_ikan || $jumlah_ikan === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        // verify tangkapan belongs to user via perjalanan
        $q = "SELECT t.id_tangkapan FROM tangkapan t JOIN perjalanan p ON t.id_perjalanan = p.id_perjalanan WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tangkapan tidak ditemukan atau tidak punya akses']);
            $s->close();
            exit();
        }
        $s->close();

        $query = "UPDATE tangkapan SET id_perjalanan = ?, jenis_ikan = ?, nama_ikan = ?, jumlah_ikan = ?, tanggal_jawa = ? WHERE id_tangkapan = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issisi', $id_perjalanan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa, $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'affected' => $stmt->affected_rows]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    if ($method === 'DELETE') {
        $id = isset($input['id_tangkapan']) ? (int)$input['id_tangkapan'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }

        $q = "SELECT t.id_tangkapan FROM tangkapan t JOIN perjalanan p ON t.id_perjalanan = p.id_perjalanan WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tangkapan tidak ditemukan atau tidak punya akses']);
            $s->close();
            exit();
        }
        $s->close();

        $query = "DELETE FROM tangkapan WHERE id_tangkapan = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'affected' => $stmt->affected_rows]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();

?>
