<?php

/**
 * CRUD API untuk tabel `tangkapan` (relasi ke catatan_memancing)
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';
setCorsHeaders();
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
            $query = "SELECT t.id_tangkapan, t.id_catatan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                      FROM tangkapan t
                      JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                      JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
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

        $query = "SELECT t.id_tangkapan, t.id_catatan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                  FROM tangkapan t
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
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

        // verify catatan_memancing belongs to user via perjalanan
        $q = "SELECT c.id_catatan FROM catatan_memancing c 
              JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan 
              WHERE c.id_catatan = ? AND p.id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id_catatan, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Catatan memancing tidak valid']);
            $s->close();
            exit();
        }
        $s->close();

        $query = "INSERT INTO tangkapan (id_catatan, jenis_ikan, nama_ikan, jumlah_ikan, tanggal_jawa) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issis', $id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa);
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

        // verify tangkapan belongs to user via catatan_memancing -> perjalanan
        $q = "SELECT t.id_tangkapan FROM tangkapan t 
              JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
              JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
              WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
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

        $query = "UPDATE tangkapan SET id_catatan = ?, jenis_ikan = ?, nama_ikan = ?, jumlah_ikan = ?, tanggal_jawa = ? WHERE id_tangkapan = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issisi', $id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa, $id);
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

        $q = "SELECT t.id_tangkapan FROM tangkapan t 
              JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
              JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
              WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
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

