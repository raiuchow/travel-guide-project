<?php
// models/CostEstimateModel.php

require_once __DIR__ . '/../config/database.php';

class CostEstimateModel {

    private PDO $db;

    // Fallback mapping when cost_estimates table has no entry for the post.
    public const LEVEL_MAP = [
        'low'    => 500,
        'medium' => 1500,
        'high'   => 3000,
    ];

    public function __construct() {
        $this->db = getDB();
    }

    /**
     * Returns base cost + currency for a post, or null if not found.
     * Falls back to cost_level mapping if no row in cost_estimates.
     */
    public function getByPost(int $postId, string $costLevel = ''): array {
        $stmt = $this->db->prepare(
            "SELECT base_cost, currency, last_updated
             FROM cost_estimates
             WHERE post_id = :post_id
             LIMIT 1"
        );
        $stmt->execute([':post_id' => $postId]);
        $row = $stmt->fetch();

        if ($row) {
            return [
                'base_cost'    => (float) $row['base_cost'],
                'currency'     => $row['currency'],
                'last_updated' => $row['last_updated'],
                'source'       => 'db',
            ];
        }

        // Fallback to level mapping
        $base = self::LEVEL_MAP[strtolower($costLevel)] ?? 1500;
        return [
            'base_cost'    => $base,
            'currency'     => 'USD',
            'last_updated' => null,
            'source'       => 'estimate',
        ];
    }
}
