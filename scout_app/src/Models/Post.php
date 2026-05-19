<?php
/**
 * Post Model
 * SOLID: Single Responsibility - শুধুমাত্র posts table এর operations
 */

namespace App\Models;

class Post extends BaseModel {

    /**
     * Get approved posts by scout ID
     */
    public function getApprovedByScout(int $scoutId): array {
        $sql = "SELECT * FROM posts 
                WHERE scout_id = :id AND status = 'approved' 
                ORDER BY created_at DESC";
        
        return $this->fetchAll($sql, [':id' => $scoutId]);
    }

    /**
     * Get single post by ID
     */
    public function getById(int $id): ?array {
        $sql = "SELECT * FROM posts WHERE id = :id LIMIT 1";
        return $this->fetchOne($sql, [':id' => $id]);
    }

    /**
     * Get post by ID for specific scout
     */
    public function getByIdForScout(int $id, int $scoutId): ?array {
        $sql = "SELECT * FROM posts 
                WHERE id = :id AND scout_id = :scout_id 
                LIMIT 1";
        
        return $this->fetchOne($sql, [
            ':id'       => $id,
            ':scout_id' => $scoutId
        ]);
    }
}
