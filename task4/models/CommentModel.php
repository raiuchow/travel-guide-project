<?php
// models/CommentModel.php

require_once __DIR__ . '/../config/database.php';

class CommentModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // ── Read ──────────────────────────────────────────────────────────────────

    public function getCommentsByPost(int $postId): array {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.content, c.created_at,
                    u.name AS reviewer_name, c.user_id
             FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.post_id = :post_id
             ORDER BY c.created_at ASC"
        );
        $stmt->execute([':post_id' => $postId]);
        return $stmt->fetchAll();
    }

    // ── Create ────────────────────────────────────────────────────────────────

    /**
     * Returns the newly-inserted comment row (with reviewer_name) or false on failure.
     */
    public function addComment(int $postId, int $userId, string $content): array|false {
        $stmt = $this->db->prepare(
            "INSERT INTO comments (post_id, user_id, content, created_at)
             VALUES (:post_id, :user_id, :content, NOW())"
        );
        $ok = $stmt->execute([
            ':post_id' => $postId,
            ':user_id' => $userId,
            ':content' => $content,
        ]);
        if (!$ok) return false;

        $newId = (int) $this->db->lastInsertId();
        return $this->getCommentById($newId);
    }

    // ── Delete ────────────────────────────────────────────────────────────────

    /**
     * Only deletes if the comment belongs to $userId (ownership check).
     */
    public function deleteComment(int $commentId, int $userId): bool {
        $stmt = $this->db->prepare(
            "DELETE FROM comments WHERE id = :id AND user_id = :user_id"
        );
        $stmt->execute([':id' => $commentId, ':user_id' => $userId]);
        return $stmt->rowCount() > 0;
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function getCommentById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT c.id, c.content, c.created_at,
                    u.name AS reviewer_name, c.user_id
             FROM comments c
             JOIN users u ON u.id = c.user_id
             WHERE c.id = :id"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
