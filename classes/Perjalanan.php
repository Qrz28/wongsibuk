<?php
/**
 * Perjalanan (Trip) Model Class
 * Fishing Log Application - OOP Version
 */

class Perjalanan {
    private $db;
    private $conn;

    public function __construct($database) {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
    }

    public function getAll($userId) {
        $query = "SELECT id_perjalanan, waktu_mulai, waktu_selesai, jarak_lokasi 
                  FROM perjalanan WHERE id_pengguna = ? ORDER BY waktu_mulai DESC";
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
        $query = "SELECT id_perjalanan, id_pengguna, waktu_mulai, waktu_selesai, jarak_lokasi 
                  FROM perjalanan WHERE id_perjalanan = ? AND id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function create($userId, $waktu_mulai, $waktu_selesai, $jarak_lokasi) {
        $query = "INSERT INTO perjalanan (id_pengguna, waktu_mulai, waktu_selesai, jarak_lokasi) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('issd', $userId, $waktu_mulai, $waktu_selesai, $jarak_lokasi);

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

    public function update($id, $userId, $waktu_mulai, $waktu_selesai, $jarak_lokasi) {
        $query = "UPDATE perjalanan SET waktu_mulai = ?, waktu_selesai = ?, jarak_lokasi = ? 
                  WHERE id_perjalanan = ? AND id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ssdii', $waktu_mulai, $waktu_selesai, $jarak_lokasi, $id, $userId);

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
        $query = "DELETE FROM perjalanan WHERE id_perjalanan = ? AND id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);

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

    public function verifyOwnership($id, $userId) {
        $query = "SELECT id_perjalanan FROM perjalanan WHERE id_perjalanan = ? AND id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
