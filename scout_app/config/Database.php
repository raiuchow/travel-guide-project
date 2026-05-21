<?php

namespace Config;

class Database {
    private static ?Database $instance = null;
    private \mysqli $connection;

    private string $host     = 'localhost';   
    private string $dbname   = 'scout_db';    
    private string $username = 'root';        
    private string $password = '';            
    private string $charset  = 'utf8mb4';

    private function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        try {
            $this->connection = new \mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->dbname
            );

            $this->connection->set_charset($this->charset);
        } catch (\mysqli_sql_exception $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed. Please check configuration.");
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): \mysqli {
        return $this->connection;
    }
    private function __clone() {}

    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
