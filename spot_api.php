<?php
/**
 * CRUD API untuk tabel `spot_memancing`
 * Fishing Log Application
 */

header('Content-Type: application/json; charset=utf-8');

require_once 'config.php';
setCorsHeaders();
session_start();

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if user is logged in
if (!isset($_SESSION['id_pengguna'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true) ?? [];

try {
    if ($method === 'GET') {
        // Get single spot or all spots
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $query = "SELECT id_spot, alamat, deskripsi_spot, jenis_spot, jarak_lokasi FROM spot_memancing WHERE id_spot = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
            $stmt->close();
            exit();
        }

        // Get all spots
        $query = "SELECT id_spot, alamat, deskripsi_spot, jenis_spot, jarak_lokasi FROM spot_memancing ORDER BY id_spot DESC";
        $stmt = $conn->prepare($query);
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
        // Create new spot
        $alamat = trim($input['alamat'] ?? '');
        $deskripsi_spot = trim($input['deskripsi_spot'] ?? '');
        $jenis_spot = trim($input['jenis_spot'] ?? '');
        $jarak_lokasi = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : 0;

        // Validation
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

        // Insert
        $query = "INSERT INTO spot_memancing (alamat, deskripsi_spot, jenis_spot, jarak_lokasi) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param('sssd', $alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi);
        
        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode([
                'success' => true, 
                'message' => 'Spot memancing berhasil ditambahkan',
                'id' => $conn->insert_id
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan: ' . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    if ($method === 'PUT') {
        // Update spot
        $id = isset($input['id_spot']) ? (int)$input['id_spot'] : null;
        $alamat = trim($input['alamat'] ?? '');
        $deskripsi_spot = trim($input['deskripsi_spot'] ?? '');
        $jenis_spot = trim($input['jenis_spot'] ?? '');
        $jarak_lokasi = isset($input['jarak_lokasi']) ? (float)$input['jarak_lokasi'] : 0;

        // Validation
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

        // Check if spot exists
        $checkQuery = "SELECT id_spot FROM spot_memancing WHERE id_spot = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        
        if ($checkRes->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Spot tidak ditemukan']);
            $checkStmt->close();
            exit();
        }
        $checkStmt->close();

        // Update
        $query = "UPDATE spot_memancing SET alamat = ?, deskripsi_spot = ?, jenis_spot = ?, jarak_lokasi = ? WHERE id_spot = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param('sssdi', $alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi, $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Spot memancing berhasil diperbarui',
                'affected' => $stmt->affected_rows
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal memperbarui: ' . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    if ($method === 'DELETE') {
        // Delete spot
        $id = isset($input['id_spot']) ? (int)$input['id_spot'] : null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID spot harus diberikan']);
            exit();
        }

        // Check if spot exists
        $checkQuery = "SELECT id_spot FROM spot_memancing WHERE id_spot = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param('i', $id);
        $checkStmt->execute();
        $checkRes = $checkStmt->get_result();
        
        if ($checkRes->num_rows === 0) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Spot tidak ditemukan']);
            $checkStmt->close();
            exit();
        }
        $checkStmt->close();

        // Delete
        $query = "DELETE FROM spot_memancing WHERE id_spot = ?";
        $stmt = $conn->prepare($query);
        
        if (!$stmt) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            exit();
        }

        $stmt->bind_param('i', $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true, 
                'message' => 'Spot memancing berhasil dihapus',
                'affected' => $stmt->affected_rows
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal menghapus: ' . $stmt->error]);
        }
        $stmt->close();
        exit();
    }

    // Method not allowed
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
