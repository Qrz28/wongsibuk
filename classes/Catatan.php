<?php
/**
 * Catatan Memancing Model Class
 * Fishing Log Application - OOP Version
 */

class Catatan {
    private $db;
    private $conn;

    public function __construct($database) {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
    }

    public function getAll($userId) {
        $query = "SELECT c.id_catatan, c.id_perjalanan, c.id_spot, c.catatan, p.waktu_mulai, s.alamat
                  FROM catatan_memancing c
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  LEFT JOIN spot_memancing s ON c.id_spot = s.id_spot
                  WHERE p.id_pengguna = ?
                  ORDER BY c.id_catatan DESC";
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
        $query = "SELECT c.id_catatan, c.id_perjalanan, c.id_spot, c.catatan, p.waktu_mulai, s.alamat
                  FROM catatan_memancing c
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan
                  LEFT JOIN spot_memancing s ON c.id_spot = s.id_spot
                  WHERE c.id_catatan = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();
        $stmt->close();
        return $data;
    }

    public function create($id_perjalanan, $id_spot, $catatan, $userId) {
        // Verify perjalanan belongs to user
        if (!$this->verifyPerjalananOwnership($id_perjalanan, $userId)) {
            return ['success' => false, 'message' => 'Perjalanan tidak valid'];
        }

        $query = "INSERT INTO catatan_memancing (id_perjalanan, id_spot, catatan) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iis', $id_perjalanan, $id_spot, $catatan);

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

    public function update($id, $id_perjalanan, $id_spot, $catatan, $userId) {
        // Verify catatan belongs to user via perjalanan
        if (!$this->verifyOwnership($id, $userId)) {
            return ['success' => false, 'message' => 'Catatan tidak ditemukan atau tidak punya akses'];
        }

        $query = "UPDATE catatan_memancing SET id_perjalanan = ?, id_spot = ?, catatan = ? WHERE id_catatan = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('iisi', $id_perjalanan, $id_spot, $catatan, $id);

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
        // Verify catatan belongs to user via perjalanan
        if (!$this->verifyOwnership($id, $userId)) {
            return ['success' => false, 'message' => 'Catatan tidak ditemukan atau tidak punya akses'];
        }

        $query = "DELETE FROM catatan_memancing WHERE id_catatan = ?";
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
        $query = "SELECT c.id_catatan FROM catatan_memancing c 
                  JOIN perjalanan p ON c.id_perjalanan = p.id_perjalanan 
                  WHERE c.id_catatan = ? AND p.id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    private function verifyPerjalananOwnership($id_perjalanan, $userId) {
        $query = "SELECT id_perjalanan FROM perjalanan WHERE id_perjalanan = ? AND id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ii', $id_perjalanan, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }
}
