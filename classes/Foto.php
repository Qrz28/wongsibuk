<?php
/**
 * Foto Model Class
 * Fishing Log Application - OOP Version
 */

class Foto {
    private $db;
    private $conn;
    private $uploadDir;

    public function __construct($database, $uploadDir = 'uploads/') {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
        $this->uploadDir = $uploadDir;

        // Create uploads directory if not exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    public function getAll($userId) {
        $query = "SELECT f.id_foto, f.id_tangkapan, f.deskripsi, f.tanggal_ambil, f.nama_file
                  FROM foto f
                  JOIN tangkapan t ON f.id_tangkapan = t.id_tangkapan
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE p.id_pengguna = ?
                  ORDER BY f.tanggal_ambil DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function getById($id, $userId) {
        $query = "SELECT f.id_foto, f.id_tangkapan, f.deskripsi, f.tanggal_ambil, f.nama_file
                  FROM foto f
                  JOIN tangkapan t ON f.id_tangkapan = t.id_tangkapan
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE f.id_foto = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function getByTangkapan($id_tangkapan, $userId) {
        // Verify tangkapan belongs to user
        if (!$this->verifyTangkapanOwnership($id_tangkapan, $userId)) {
            return ['success' => false, 'message' => 'Akses ditolak'];
        }

        $query = "SELECT id_foto, id_tangkapan, deskripsi, tanggal_ambil, nama_file 
                  FROM foto WHERE id_tangkapan = ? ORDER BY tanggal_ambil DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id_tangkapan);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function upload($id_tangkapan, $file, $deskripsi, $userId) {
        // Verify tangkapan belongs to user
        if (!$this->verifyTangkapanOwnership($id_tangkapan, $userId)) {
            return ['success' => false, 'message' => 'Tangkapan tidak valid'];
        }

        // Do not trust the browser-provided MIME type or filename extension.
        if (!isset($file['tmp_name'], $file['size']) || !is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Upload file tidak valid'];
        }
        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!isset($allowedTypes[$mimeType])) {
            return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
        }

        if ($file['size'] < 1 || $file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 5MB'];
        }

        // Generate server-controlled filename: a valid image can never become executable by extension.
        $filename = 'fish_' . bin2hex(random_bytes(16)) . '.' . $allowedTypes[$mimeType];
        $target_path = $this->uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $query = "INSERT INTO foto (id_tangkapan, deskripsi, nama_file) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param('iss', $id_tangkapan, $deskripsi, $filename);

            if ($stmt->execute()) {
                $id = $this->conn->insert_id;
                $stmt->close();
                return ['success' => true, 'id' => $id, 'filename' => $filename];
            } else {
                $stmt->close();
                if (is_file($target_path)) { unlink($target_path); }
                return ['success' => false, 'message' => 'Gagal menyimpan data foto'];
            }
        } else {
            return ['success' => false, 'message' => 'Gagal mengupload file'];
        }
    }

    public function delete($id, $userId) {
        // Get file info first
        $query = "SELECT f.nama_file FROM foto f 
                  JOIN tangkapan t ON f.id_tangkapan = t.id_tangkapan
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE f.id_foto = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            $stmt->close();
            return ['success' => false, 'message' => 'Foto tidak ditemukan atau tidak punya akses'];
        }

        $row = $result->fetch_assoc();
        $filename = $row['nama_file'];
        $stmt->close();

        // Delete from database
        $query = "DELETE FROM foto WHERE id_foto = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            // Delete file from disk
            $file_path = $this->uploadDir . $filename;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $affected = $stmt->affected_rows;
            $stmt->close();
            return ['success' => true, 'affected' => $affected];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => $error];
        }
    }

    private function verifyTangkapanOwnership($id_tangkapan, $userId) {
        $query = "SELECT t.id_tangkapan FROM tangkapan t 
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id_tangkapan, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
