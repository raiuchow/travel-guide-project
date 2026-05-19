<?php
/**
 * Base Model Class
 * SOLID: Single Responsibility - Database operations এর base functionality
 * SOLID: Open/Closed - Extension এর জন্য open, modification এর জন্য closed
 */

namespace App\Models;

use PDO;

abstract class BaseModel {
    protected PDO $db;

    /**
     * Constructor - Dependency Injection pattern
     */
    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Execute a prepared statement
     */
    protected function execute(string $sql, array $params = []): \PDOStatement {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch single row
     */
    protected function fetchOne(string $sql, array $params = []): ?array {
        $stmt = $this->execute($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows
     */
    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->execute($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * Get last insert ID
     */
    protected function lastInsertId(): int {
        return (int) $this->db->lastInsertId();
    }
}
