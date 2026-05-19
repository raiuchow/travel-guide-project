<?php
/**
 * PostRequest Model
 * SOLID: Single Responsibility - শুধুমাত্র post_requests table এর operations
 * SOLID: Dependency Inversion - PDO dependency inject করা হয়
 */

namespace App\Models;

class PostRequest extends BaseModel {

    /**
     * Create a new post request
     */
    public function create(int $scoutId, array $postData, ?int $originalPostId = null): int {
        $sql = "INSERT INTO post_requests 
                    (scout_id, post_data, original_post_id, status, requested_at)
                VALUES 
                    (:scout_id, :post_data, :original_post_id, 'pending', NOW())";

        $this->execute($sql, [
            ':scout_id'         => $scoutId,
            ':post_data'        => json_encode($postData, JSON_UNESCAPED_UNICODE),
            ':original_post_id' => $originalPostId,
        ]);

        return $this->lastInsertId();
    }

    /**
     * Get all requests by scout ID
     */
    public function getAllByScout(int $scoutId): array {
        $sql = "SELECT * FROM post_requests 
                WHERE scout_id = :id 
                ORDER BY requested_at DESC";
        
        $rows = $this->fetchAll($sql, [':id' => $scoutId]);

        return array_map(function (array $row): array {
            $row['post_data'] = json_decode($row['post_data'], true) ?? [];
            return $row;
        }, $rows);
    }

    /**
     * Get single request by ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM post_requests WHERE id = :id LIMIT 1";
        $row = $this->fetchOne($sql, [':id' => $id]);
        
        if (!$row) return null;

        $row['post_data'] = json_decode($row['post_data'], true) ?? [];
        return $row;
    }

    /**
     * Get request by ID for specific scout (ownership check)
     */
    public function getByIdForScout(int $id, int $scoutId): ?array {
        $sql = "SELECT * FROM post_requests 
                WHERE id = :id AND scout_id = :scout_id 
                LIMIT 1";
        
        $row = $this->fetchOne($sql, [
            ':id'       => $id,
            ':scout_id' => $scoutId
        ]);

        if (!$row) return null;

        $row['post_data'] = json_decode($row['post_data'], true) ?? [];
        return $row;
    }

    /**
     * Update post request (only if pending)
     */
    public function update(int $id, int $scoutId, array $postData): bool {
        $sql = "UPDATE post_requests 
                SET post_data = :post_data
                WHERE id = :id AND scout_id = :scout_id AND status = 'pending'";
        
        $stmt = $this->execute($sql, [
            ':post_data' => json_encode($postData, JSON_UNESCAPED_UNICODE),
            ':id'        => $id,
            ':scout_id'  => $scoutId,
        ]);

        return $stmt->rowCount() > 0;
    }

    /**
     * Delete post request (only if pending)
     */
    public function delete(int $id, int $scoutId): bool {
        $sql = "DELETE FROM post_requests 
                WHERE id = :id AND scout_id = :scout_id AND status = 'pending'";
        
        $stmt = $this->execute($sql, [
            ':id'       => $id,
            ':scout_id' => $scoutId
        ]);

        return $stmt->rowCount() > 0;
    }
}
