<?php
/**
 * User Model Class
 * Fishing Log Application - OOP Version
 */

class User {
    private $db;
    private $conn;

    public function __construct($database) {
        $this->db = $database;
        $this->conn = $this->db->getConnection();
    }

    public function register($nama, $email, $password) {
        // Check if email already exists
        $checkQuery = "SELECT id_pengguna FROM pengguna WHERE email = ?";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bind_param('s', $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            $checkStmt->close();
            return ['success' => false, 'message' => 'Email sudah terdaftar'];
        }
        $checkStmt->close();

        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Insert new user
        $insertQuery = "INSERT INTO pengguna (nama, email, password) VALUES (?, ?, ?)";
        $insertStmt = $this->conn->prepare($insertQuery);
        $insertStmt->bind_param('sss', $nama, $email, $hashedPassword);

        if ($insertStmt->execute()) {
            $newUserId = $insertStmt->insert_id;
            $insertStmt->close();
            return [
                'success' => true,
                'id_pengguna' => $newUserId,
                'nama' => $nama,
                'email' => $email
            ];
        } else {
            $insertStmt->close();
            return ['success' => false, 'message' => 'Gagal mendaftarkan akun'];
        }
    }

    public function login($email, $password) {
        $query = "SELECT id_pengguna, nama, email, password FROM pengguna WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $stmt->close();

            if (password_verify($password, $user['password'])) {
                return [
                    'success' => true,
                    'id_pengguna' => $user['id_pengguna'],
                    'nama' => $user['nama'],
                    'email' => $user['email']
                ];
            }
        }
        $stmt->close();
        return ['success' => false, 'message' => 'Email atau password salah'];
    }

    public function getById($id) {
        $query = "SELECT id_pengguna, nama, email FROM pengguna WHERE id_pengguna = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }
}
