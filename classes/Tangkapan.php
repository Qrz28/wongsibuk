<?php
/**
 * Tangkapan (Catch) Model Class
 * Fishing Log Application - OOP Version
 */

class Tangkapan {
    private $db;
    private $conn;

    public function __construct($database) {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
    }

    public function getAll($userId) {
        $query = "SELECT t.id_tangkapan, t.id_catatan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                  FROM tangkapan t
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE p.id_pengguna = ?
                  ORDER BY t.id_tangkapan DESC";
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
        $query = "SELECT t.id_tangkapan, t.id_catatan, t.jenis_ikan, t.nama_ikan, t.jumlah_ikan, t.tanggal_jawa
                  FROM tangkapan t
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function create($id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa, $userId) {
        // Verify catatan_memancing belongs to user via perjalanan
        if (!$this->verifyCatatanOwnership($id_catatan, $userId)) {
            return ['success' => false, 'message' => 'Catatan memancing tidak valid'];
        }

        $query = "INSERT INTO tangkapan (id_catatan, jenis_ikan, nama_ikan, jumlah_ikan, tanggal_jawa) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('issis', $id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa);

        if ($stmt->execute()) {
            $id = $this->conn->insert_id;
            $stmt->close();
            return ['success' => true, 'id' => $id];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => $error];
        }
    }

    public function update($id, $id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa, $userId) {
        // Verify tangkapan belongs to user via catatan_memancing -> perjalanan
        if (!$this->verifyOwnership($id, $userId)) {
            return ['success' => false, 'message' => 'Tangkapan tidak ditemukan atau tidak punya akses'];
        }

        $query = "UPDATE tangkapan SET id_catatan = ?, jenis_ikan = ?, nama_ikan = ?, jumlah_ikan = ?, tanggal_jawa = ? WHERE id_tangkapan = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('issisi', $id_catatan, $jenis_ikan, $nama_ikan, $jumlah_ikan, $tanggal_jawa, $id);

        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            return ['success' => true, 'affected' => $affected];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => $error];
        }
    }

    public function delete($id, $userId) {
        // Verify tangkapan belongs to user via catatan_memancing -> perjalanan
        if (!$this->verifyOwnership($id, $userId)) {
            return ['success' => false, 'message' => 'Tangkapan tidak ditemukan atau tidak punya akses'];
        }

        $query = "DELETE FROM tangkapan WHERE id_tangkapan = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);

        if ($stmt->execute()) {
            $affected = $stmt->affected_rows;
            $stmt->close();
            return ['success' => true, 'affected' => $affected];
        } else {
            $error = $stmt->error;
            $stmt->close();
            return ['success' => false, 'message' => $error];
        }
    }

    private function verifyOwnership($id, $userId) {
        $query = "SELECT t.id_tangkapan FROM tangkapan t 
                  JOIN catatan_memancing c ON t.id_catatan = c.id_catatan
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  WHERE t.id_tangkapan = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    private function verifyCatatanOwnership($id_catatan, $userId) {
        $query = "SELECT c.id_catatan FROM catatan_memancing c 
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan 
                  WHERE c.id_catatan = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id_catatan, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
