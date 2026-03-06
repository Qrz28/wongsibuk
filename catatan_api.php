<?php
/**
 * CRUD API untuk tabel `catatan_memancing`
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
            $query = "SELECT c.id_catatan, c.id_perjalanan, c.id_spot, c.catatan, p.waktu_mulai, s.alamat
                      FROM catatan_memancing c
                      JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                      LEFT JOIN spot_memancing s ON c.id_spot = s.id_spot
                      WHERE c.id_catatan = ? AND p.id_pengguna = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $id, $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
            $stmt->close();
            exit();
        }

        // Get all catatan for this user
        $query = "SELECT c.id_catatan, c.id_perjalanan, c.id_spot, c.catatan, p.waktu_mulai, s.alamat
                  FROM catatan_memancing c
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  LEFT JOIN spot_memancing s ON c.id_spot = s.id_spot
                  WHERE p.id_pengguna = ?
                  ORDER BY c.id_catatan DESC";
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
        $id_spot = isset($input['id_spot']) ? (int)$input['id_spot'] : null;
        $catatan = trim($input['catatan'] ?? '');

        if (!$id_perjalanan || !$id_spot) {
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

        $query = "INSERT INTO catatan_memancing (id_perjalanan, id_spot, catatan) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iis', $id_perjalanan, $id_spot, $catatan);
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
        $id = isset($input['id_catatan']) ? (int)$input['id_catatan'] : null;
        $id_perjalanan = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        $id_spot = isset($input['id_spot']) ? (int)$input['id_spot'] : null;
        $catatan = trim($input['catatan'] ?? '');

        if (!$id || !$id_perjalanan || !$id_spot) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        // verify catatan belongs to user via perjalanan
        $q = "SELECT c.id_catatan FROM catatan_memancing c 
              JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan 
              WHERE c.id_catatan = ? AND p.id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Catatan tidak ditemukan atau tidak punya akses']);
            $s->close();
            exit();
        }
        $s->close();

        $query = "UPDATE catatan_memancing SET id_perjalanan = ?, id_spot = ?, catatan = ? WHERE id_catatan = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('iisi', $id_perjalanan, $id_spot, $catatan, $id);
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
        $id = isset($input['id_catatan']) ? (int)$input['id_catatan'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }

        $q = "SELECT c.id_catatan FROM catatan_memancing c 
              JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan 
              WHERE c.id_catatan = ? AND p.id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Catatan tidak ditemukan atau tidak punya akses']);
            $s->close();
            exit();
        }
        $s->close();

        $query = "DELETE FROM catatan_memancing WHERE id_catatan = ?";
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

