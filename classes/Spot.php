<?php
/**
 * Spot Memancing Model Class
 * Fishing Log Application - OOP Version
 */

class Spot {
    private $db;
    private $conn;

    public function __construct($database) {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
    }

    public function getAll() {
        $query = "SELECT id_spot, alamat, deskripsi_spot, jenis_spot, jarak_lokasi 
                  FROM spot_memancing ORDER BY id_spot DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        $stmt->close();
        return $rows;
    }

    public function getById($id) {
        $query = "SELECT id_spot, alamat, deskripsi_spot, jenis_spot, jarak_lokasi 
                  FROM spot_memancing WHERE id_spot = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function create($alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi = 0) {
        $query = "INSERT INTO spot_memancing (alamat, deskripsi_spot, jenis_spot, jarak_lokasi) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssd', $alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi);

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

    public function update($id, $alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi = 0) {
        $query = "UPDATE spot_memancing SET alamat = ?, deskripsi_spot = ?, jenis_spot = ?, jarak_lokasi = ? 
                  WHERE id_spot = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('sssdi', $alamat, $deskripsi_spot, $jenis_spot, $jarak_lokasi, $id);

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

    public function delete($id) {
        $query = "DELETE FROM spot_memancing WHERE id_spot = ?";
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

    public function exists($id) {
        $query = "SELECT id_spot FROM spot_memancing WHERE id_spot = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
