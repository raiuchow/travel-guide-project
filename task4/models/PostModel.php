<?php
// models/PostModel.php

require_once __DIR__ . '/../config/database.php';

class PostModel {

    private PDO $db;

    public function __construct() {
        $this->db = getDB();
    }

    // ── Browse ────────────────────────────────────────────────────────────────

    /**
     * Return all approved posts with optional limit/offset.
     */
    public function getApprovedPosts(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare(
            "SELECT p.*, u.name AS scout_name
             FROM posts p
             JOIN users u ON u.id = p.scout_id
             WHERE p.status = 'approved'
             ORDER BY p.created_at DESC
             LIMIT :lim OFFSET :off"
        );
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countApproved(): int {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM posts WHERE status = 'approved'"
        )->fetchColumn();
    }

    // ── Detail ────────────────────────────────────────────────────────────────

    public function getPostById(int $id): array|false {
        $stmt = $this->db->prepare(
            "SELECT p.*, u.name AS scout_name
             FROM posts p
             JOIN users u ON u.id = p.scout_id
             WHERE p.id = :id AND p.status = 'approved'"
        );
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // ── Search & Filter ───────────────────────────────────────────────────────

    /**
     * Live search by title or country (used by AJAX).
     */
    public function searchPosts(string $q, int $limit = 20): array {
        $like = '%' . $q . '%';
        $stmt = $this->db->prepare(
            "SELECT id, title, country, genre, cost_level, short_history
             FROM posts
             WHERE status = 'approved'
               AND (title LIKE :q1 OR country LIKE :q2)
             ORDER BY created_at DESC
             LIMIT :lim"
        );
        $stmt->bindValue(':q1', $like);
        $stmt->bindValue(':q2', $like);
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Filtered listing (country, genre[], cost_level) for AJAX /api/posts/filter.
     */
    public function filterPosts(
        string $country  = '',
        array  $genres   = [],
        string $costLevel = '',
        int    $limit    = 20
    ): array {
        $conditions = ["p.status = 'approved'"];
        $params     = [];

        if ($country !== '') {
            $conditions[] = 'p.country = :country';
            $params[':country'] = $country;
        }
        if ($costLevel !== '') {
            $conditions[] = 'p.cost_level = :cost_level';
            $params[':cost_level'] = $costLevel;
        }
        if (!empty($genres)) {
            // Build IN placeholders
            $placeholders = [];
            foreach ($genres as $i => $g) {
                $key = ':genre' . $i;
                $placeholders[] = $key;
                $params[$key] = $g;
            }
            $conditions[] = 'p.genre IN (' . implode(',', $placeholders) . ')';
        }

        $where = implode(' AND ', $conditions);
        $stmt  = $this->db->prepare(
            "SELECT p.id, p.title, p.country, p.genre, p.cost_level, p.short_history, p.created_at
             FROM posts p
             WHERE {$where}
             ORDER BY p.created_at DESC
             LIMIT :lim"
        );
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // ── Distinct values for filter dropdowns ─────────────────────────────────

    public function getDistinctCountries(): array {
        return $this->db->query(
            "SELECT DISTINCT country FROM posts WHERE status = 'approved' ORDER BY country"
        )->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getDistinctGenres(): array {
        return $this->db->query(
            "SELECT DISTINCT genre FROM posts WHERE status = 'approved' ORDER BY genre"
        )->fetchAll(PDO::FETCH_COLUMN);
    }
}
