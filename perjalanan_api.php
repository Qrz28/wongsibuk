<?php
/**
 * CRUD API untuk tabel `perjalanan`
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
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

// Read input for POST/PUT/DELETE
$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    if ($method === 'GET') {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $query = "SELECT id_perjalanan, id_pengguna, waktu_mulai, waktu_selesai, jarak_lokasi FROM perjalanan WHERE id_perjalanan = ? AND id_pengguna = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $id, $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
            $stmt->close();
            exit();
        }

        $query = "SELECT id_perjalanan, waktu_mulai, waktu_selesai, jarak_lokasi FROM perjalanan WHERE id_pengguna = ? ORDER BY waktu_mulai DESC";
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
        // Create
        $waktu_mulai = $input['waktu_mulai'] ?? null;
        $waktu_selesai = $input['waktu_selesai'] ?? null;
        $jarak = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : null;

        if (!$waktu_mulai || !$waktu_selesai || $jarak === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $query = "INSERT INTO perjalanan (id_pengguna, waktu_mulai, waktu_selesai, jarak_lokasi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('issd', $user_id, $waktu_mulai, $waktu_selesai, $jarak);
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
        $id = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        $waktu_mulai = $input['waktu_mulai'] ?? null;
        $waktu_selesai = $input['waktu_selesai'] ?? null;
        $jarak = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : null;

        if (!$id || !$waktu_mulai || !$waktu_selesai || $jarak === null) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Field tidak lengkap']);
            exit();
        }

        $query = "UPDATE perjalanan SET waktu_mulai = ?, waktu_selesai = ?, jarak_lokasi = ? WHERE id_perjalanan = ? AND id_pengguna = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ssdii', $waktu_mulai, $waktu_selesai, $jarak, $id, $user_id);
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
        $id = isset($input['id_perjalanan']) ? (int)$input['id_perjalanan'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }
        $query = "DELETE FROM perjalanan WHERE id_perjalanan = ? AND id_pengguna = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('ii', $id, $user_id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'affected' => $stmt->affected_rows]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    // Method not allowed
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();

?>
