<?php
class Database {
    private $host = 'localhost';
    private $db = 'travel_guide';
    private $user = 'root';
    private $pass = '';

    public function connect() {
        $conn = mysqli_connect($this->host, $this->user, $this->pass, $this->db);

        if (!$conn) {
            die('Database connection failed');
        }

        mysqli_set_charset($conn, 'utf8mb4');
        return $conn;
    }
}
