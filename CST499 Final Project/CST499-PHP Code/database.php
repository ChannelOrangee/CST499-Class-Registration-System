<?php
class Database {
    private string $host = "localhost";
    private string $user = "root";
    private string $pass = "";
    private string $db   = "CST499";   // ✅ make sure this matches phpMyAdmin
    private int $port    = 3306;

    private ?mysqli $conn = null;

    public function getConnection(): mysqli {
        if ($this->conn !== null) {
            return $this->conn;
        }

        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db, $this->port);
        $this->conn->set_charset("utf8mb4");

        return $this->conn;
    }
}