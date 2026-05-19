<?php
/**
 * Database Connection Class
 * SOLID: Single Responsibility - শুধুমাত্র database connection manage করে
 * Pattern: Singleton - একটি মাত্র instance থাকবে
 */

namespace Config;

use PDO;
use PDOException;

class Database {
    private static ?Database $instance = null;
    private PDO $connection;

    // আপনার database credentials এখানে দিন
    private string $host     = 'localhost';      // MySQL host
    private string $dbname   = 'scout_db';       // Database name
    private string $username = 'root';           // MySQL username
    private string $password = '';               // MySQL password (খালি থাকলে '' রাখুন)
    private string $charset  = 'utf8mb4';

    /**
     * Private constructor - Singleton pattern
     */
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed. Please check configuration.");
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection
     */
    public function getConnection(): PDO {
        return $this->connection;
    }

    /**
     * Prevent cloning
     */
    private function __clone() {}

    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
