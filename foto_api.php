<?php
/**
 * CRUD API untuk tabel `foto` (upload foto tangkapan)
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

// Create uploads directory if not exists
$upload_dir = 'uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

try {
    if ($method === 'GET') {
        if (isset($_GET['id_tangkapan'])) {
            $id_tangkapan = (int)$_GET['id_tangkapan'];
            
            // Verify tangkapan belongs to user via catatan_memancing -> perjalanan
            $q = "SELECT t.id_tangkapan FROM tangkapan t 
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
            $s = $conn->prepare($q);
            $s->bind_param('ii', $id_tangkapan, $user_id);
            $s->execute();
            $r = $s->get_result();
            if ($r->num_rows === 0) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
                $s->close();
                exit();
            }
            $s->close();

            $query = "SELECT id_foto, id_tangkapan, deskripsi, tanggal_ambil, nama_file 
                      FROM foto 
                      WHERE id_tangkapan = ?
                      ORDER BY tanggal_ambil DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('i', $id_tangkapan);
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

        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $query = "SELECT f.id_foto, f.id_tangkapan, f.deskripsi, f.tanggal_ambil, f.nama_file
                      FROM foto f
                      JOIN tangkapan t ON f.id_tangkapan = t.id_tangkapan
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                      WHERE f.id_foto = ? AND p.id_pengguna = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('ii', $id, $user_id);
            $stmt->execute();
            $res = $stmt->get_result();
            $data = $res->fetch_assoc();
            echo json_encode(['success' => true, 'data' => $data]);
            $stmt->close();
            exit();
        }

        // Get all photos for this user's catches
        $query = "SELECT f.id_foto, f.id_tangkapan, f.deskripsi, f.tanggal_ambil, f.nama_file
                  FROM foto f
                  JOIN tangkapan t ON f.id_tangkapan = t.id_tangkapan
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE p.id_pengguna = ?
                  ORDER BY f.tanggal_ambil DESC";
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
        // Handle file upload
        $id_tangkapan = isset($_POST['id_tangkapan']) ? (int)$_POST['id_tangkapan'] : null;
        $deskripsi = isset($_POST['deskripsi']) ? trim($_POST['deskripsi']) : '';
        
        if (!$id_tangkapan) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tangkapan diperlukan']);
            exit();
        }

        // Verify tangkapan belongs to user
        $q = "SELECT t.id_tangkapan FROM tangkapan t 
              JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
              JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
              WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id_tangkapan, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tangkapan tidak valid']);
            $s->close();
            exit();
        }
        $s->close();

        // Handle file upload
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'File foto diperlukan']);
            exit();
        }

        $file = $_FILES['foto'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($file['type'], $allowed_types)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Tipe file tidak diizinkan. Gunakan JPEG, PNG, GIF, atau WebP']);
            exit();
        }

        if ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5MB']);
            exit();
        }

        // Generate unique filename
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid('fish_') . '.' . $ext;
        $target_path = $upload_dir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $query = "INSERT INTO foto (id_tangkapan, deskripsi, nama_file) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('iss', $id_tangkapan, $deskripsi, $filename);
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'id' => $conn->insert_id, 'filename' => $filename]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $stmt->error]);
            }
            $stmt->close();
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
        }
        exit();
    }

    if ($method === 'DELETE') {
        $id = isset($input['id_foto']) ? (int)$input['id_foto'] : null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
            exit();
        }

        // Get file info first
        $q = "SELECT f.nama_file FROM foto f 
              JOIN tangkapan t ON f.id_tangkapan = t.id_tangkapan
              JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
              JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
              WHERE f.id_foto = ? AND p.id_pengguna = ?";
        $s = $conn->prepare($q);
        $s->bind_param('ii', $id, $user_id);
        $s->execute();
        $r = $s->get_result();
        if ($r->num_rows === 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Foto tidak ditemukan atau tidak punya akses']);
            $s->close();
            exit();
        }
        $row = $r->fetch_assoc();
        $filename = $row['nama_file'];
        $s->close();

        // Delete from database
        $query = "DELETE FROM foto WHERE id_foto = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            // Delete file from disk
            $file_path = $upload_dir . $filename;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
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

