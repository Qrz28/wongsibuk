<?php
/**
 * Database Connection Class
 * Fishing Log Application - OOP Version
 */

class Database {
    private $host;
    private $user;
    private $pass;
    private $dbname;
    private $conn;

    public function __construct($host = null, $user = null, $pass = null, $dbname = null) {
        $this->host = $host ?? DB_HOST;
        $this->user = $user ?? DB_USER;
        $this->pass = $pass ?? DB_PASS;
        $this->dbname = $dbname ?? DB_NAME;
    }

    public function connect() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

        if ($this->conn->connect_error) {
            throw new Exception('Koneksi database gagal: ' . $this->conn->connect_error);
        }

        $this->conn->set_charset("utf8");
        return $this->conn;
    }

    public function getConnection() {
        if (!$this->conn) {
            return $this->connect();
        }
        return $this->conn;
    }

    public function close() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
