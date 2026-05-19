-- ============================================================
--  Task 4 – Database Setup
--  Run this ONCE on top of the shared schema.
--  Does NOT drop or alter any existing table.
-- ============================================================

-- Ensure cost_estimates table exists (may already exist from shared schema)
CREATE TABLE IF NOT EXISTS `cost_estimates` (
  `id`           INT UNSIGNED      NOT NULL AUTO_INCREMENT,
  `post_id`      INT UNSIGNED      NOT NULL,
  `base_cost`    DECIMAL(12,2)     NOT NULL DEFAULT 0.00,
  `currency`     VARCHAR(10)       NOT NULL DEFAULT 'USD',
  `last_updated` TIMESTAMP         NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_post` (`post_id`),
  CONSTRAINT `fk_ce_post`
    FOREIGN KEY (`post_id`) REFERENCES `posts`(`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ── Indexes to speed up Task 4 queries ──────────────────────────────────────

-- Live search on title / country
CREATE INDEX IF NOT EXISTS `idx_posts_title`
  ON `posts` (`title`(191));

CREATE INDEX IF NOT EXISTS `idx_posts_country`
  ON `posts` (`country`(100));

-- Filter by status + genre + cost_level + country
CREATE INDEX IF NOT EXISTS `idx_posts_status_genre`
  ON `posts` (`status`, `genre`);

CREATE INDEX IF NOT EXISTS `idx_posts_status_cost`
  ON `posts` (`status`, `cost_level`);

CREATE INDEX IF NOT EXISTS `idx_posts_status_country`
  ON `posts` (`status`, `country`(100));

-- Comments lookup by post
CREATE INDEX IF NOT EXISTS `idx_comments_post`
  ON `comments` (`post_id`);


-- ── Sample cost_estimate rows (optional seed data) ───────────────────────────
-- INSERT INTO cost_estimates (post_id, base_cost, currency) VALUES
--   (1, 800.00,  'USD'),
--   (2, 2500.00, 'USD'),
--   (3, 450.00,  'USD');
