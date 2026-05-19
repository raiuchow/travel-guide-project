<?php
class Database {
    private $host = 'localhost';
    private $db = 'travel_guide';
    private $user = 'root';
    private $pass = '';

    public function connect() {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset=utf8mb4";
        try {
            return new PDO($dsn, $this->user, $this->pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            die('Database connection failed');
        }
    }
}
