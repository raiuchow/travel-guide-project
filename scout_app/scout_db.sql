-- scout_db.sql
-- Run this once to set up the shared database schema

CREATE DATABASE IF NOT EXISTS scout_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE scout_db;

-- ── users ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS users (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)  NOT NULL,
    email           VARCHAR(150)  NOT NULL UNIQUE,
    password_hash   VARCHAR(255)  NOT NULL,
    role            ENUM('admin','scout','user') NOT NULL DEFAULT 'user',
    is_verified     TINYINT(1)    NOT NULL DEFAULT 0,
    profile_picture VARCHAR(255)  NULL,
    created_at      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ── posts ─────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS posts (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    scout_id            INT UNSIGNED NOT NULL,
    title               VARCHAR(150) NOT NULL,
    short_history       TEXT         NOT NULL,
    country             VARCHAR(150) NOT NULL,
    genre               ENUM('beach','mountain','city','historical','cultural','adventure','other') NOT NULL,
    cost_level          ENUM('low','medium','high') NOT NULL,
    travel_medium_info  VARCHAR(255) NOT NULL,
    status              ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (scout_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── post_requests ─────────────────────────────────────────────────────────────
-- post_data stores all place information as JSON
-- original_post_id is set when this is a change request for an existing approved post
CREATE TABLE IF NOT EXISTS post_requests (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    scout_id         INT UNSIGNED NOT NULL,
    post_data        JSON         NOT NULL,
    original_post_id INT UNSIGNED NULL,
    requested_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status           ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    FOREIGN KEY (scout_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (original_post_id) REFERENCES posts(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ── wishlist ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS wishlist (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED NOT NULL,
    post_id    INT UNSIGNED NOT NULL,
    added_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_post (user_id, post_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (post_id) REFERENCES posts(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── comments ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS comments (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id    INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED NOT NULL,
    content    TEXT         NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id)  ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── cost_estimates ────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS cost_estimates (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id      INT UNSIGNED   NOT NULL,
    base_cost    DECIMAL(10,2)  NOT NULL,
    currency     VARCHAR(10)    NOT NULL DEFAULT 'USD',
    last_updated DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── Demo Users (Plain Password for Testing) ────────────────────────────────────
-- WARNING: Plain passwords are NOT secure! Only for development/testing
-- Password: password123
INSERT IGNORE INTO users (id, name, email, password_hash, role, is_verified, created_at) VALUES
(1, 'Demo Scout', 'scout@demo.com', 'password123', 'scout', 1, '2026-01-15 10:00:00'),
(2, 'John Traveler', 'john@scout.com', 'password423', 'scout', 1, '2026-01-20 11:30:00'),
(3, 'Sarah Explorer', 'sarah@scout.com', 'password723', 'scout', 1, '2026-02-01 09:15:00'),
(4, 'Admin User', 'admin@scout.com', 'admin123', 'admin', 1, '2026-01-01 08:00:00'),
(5, 'Regular User', 'user@example.com', 'user123', 'user', 1, '2026-02-10 14:20:00');

-- ── Demo Approved Posts ──────────────────────────────────────────────────────────
INSERT IGNORE INTO posts (id, scout_id, title, short_history, country, genre, cost_level, travel_medium_info, status, created_at, updated_at) VALUES
(1, 1, 'Cox\'s Bazar Sea Beach', 'Cox\'s Bazar is the longest natural sea beach in the world, stretching 120 km. Famous for its golden sand and beautiful sunset views. A perfect destination for beach lovers and water sports enthusiasts.', 'Bangladesh - Bay of Bengal', 'beach', 'low', 'Bus from Dhaka (8-10 hours) or Flight to Cox\'s Bazar Airport', 'approved', '2026-03-01 10:00:00', '2026-03-02 15:30:00'),

(2, 2, 'Sundarbans Mangrove Forest', 'The largest mangrove forest in the world and home to the Royal Bengal Tiger. A UNESCO World Heritage Site with incredible biodiversity. Experience wildlife safari through rivers and creeks.', 'Bangladesh - Khulna Division', 'adventure', 'medium', 'Bus to Khulna + Boat journey (2-3 days tour)', 'approved', '2026-03-05 11:20:00', '2026-03-06 09:45:00'),

(3, 1, 'Sajek Valley', 'Known as the "Queen of Hills" and "Roof of Rangamati". Offers breathtaking views of clouds, mountains, and tribal culture. Perfect for nature photography and peaceful retreat.', 'Bangladesh - Rangamati Hill District', 'mountain', 'medium', 'Bus to Khagrachari + Local jeep to Sajek (10-12 hours)', 'approved', '2026-03-10 14:30:00', '2026-03-11 10:15:00'),

(4, 3, 'Srimangal Tea Gardens', 'The tea capital of Bangladesh with endless green tea gardens. Visit Lawachara National Park, experience tribal culture, and enjoy the famous seven-layer tea.', 'Bangladesh - Sylhet Division', 'cultural', 'low', 'Train from Dhaka to Srimangal (4-5 hours)', 'approved', '2026-03-15 09:00:00', '2026-03-16 16:20:00'),

(5, 2, 'Paharpur Buddhist Monastery', 'Ancient Buddhist monastery ruins from 8th century, UNESCO World Heritage Site. One of the most important archaeological sites in South Asia with rich historical significance.', 'Bangladesh - Naogaon District', 'historical', 'low', 'Bus from Dhaka to Naogaon (6-7 hours)', 'approved', '2026-03-20 10:45:00', '2026-03-21 11:30:00');

-- ── Demo Post Requests (Pending & Rejected) ──────────────────────────────────────
INSERT IGNORE INTO post_requests (id, scout_id, post_data, original_post_id, requested_at, status) VALUES
(1, 1, '{"title":"Ratargul Swamp Forest","short_history":"The only freshwater swamp forest in Bangladesh. A unique ecosystem where trees grow in water. Best visited during monsoon when the forest is flooded.","country":"Bangladesh - Sylhet Division","genre":"adventure","cost_level":"low","travel_medium_info":"Bus to Sylhet + Boat ride to Ratargul","image":null}', NULL, '2026-04-01 10:30:00', 'pending'),

(2, 2, '{"title":"Kuakata Sea Beach","short_history":"Unique beach where you can see both sunrise and sunset from the same spot. Known as the daughter of the sea. Less crowded than Cox\'s Bazar.","country":"Bangladesh - Patuakhali District","genre":"beach","cost_level":"low","travel_medium_info":"Bus from Dhaka to Kuakata (8-9 hours)","image":null}', NULL, '2026-04-05 11:15:00', 'pending'),

(3, 3, '{"title":"Bandarban Hill District","short_history":"The most remote and scenic hill district of Bangladesh. Home to indigenous tribes, waterfalls, and Buddhist temples. Adventure seekers paradise.","country":"Bangladesh - Chittagong Hill Tracts","genre":"mountain","cost_level":"medium","travel_medium_info":"Bus from Dhaka to Bandarban (10-11 hours)","image":null}', NULL, '2026-04-10 09:45:00', 'pending'),

(4, 1, '{"title":"Lalbagh Fort","short_history":"Incomplete Mughal fort complex from 17th century in Old Dhaka. Beautiful architecture with mosque, tomb, and gardens. Important historical landmark.","country":"Bangladesh - Dhaka","genre":"historical","cost_level":"low","travel_medium_info":"Rickshaw or Uber within Dhaka city","image":null}', NULL, '2026-04-12 14:20:00', 'rejected'),

(5, 2, '{"title":"Jaflong Zero Point","short_history":"Border area with India, famous for stone collection from Dawki River. Crystal clear water and mountain views. Popular tourist destination in Sylhet.","country":"Bangladesh - Sylhet Division","genre":"adventure","cost_level":"low","travel_medium_info":"Bus to Sylhet + Local transport to Jaflong","image":null}', NULL, '2026-04-15 10:00:00', 'pending'),

-- Change request for approved post
(6, 1, '{"title":"Cox\'s Bazar Sea Beach - Updated","short_history":"Cox\'s Bazar is the longest natural sea beach in the world, stretching 120 km. Famous for its golden sand, beautiful sunset views, and fresh seafood. Now includes new beach activities like parasailing and jet skiing. A perfect destination for beach lovers and water sports enthusiasts.","country":"Bangladesh - Bay of Bengal Coast","genre":"beach","cost_level":"medium","travel_medium_info":"Bus from Dhaka (8-10 hours), Flight to Cox\'s Bazar Airport, or Train to Chittagong + Bus","image":null}', 1, '2026-04-18 15:30:00', 'pending');

-- ── Demo Wishlist ─────────────────────────────────────────────────────────────────
INSERT IGNORE INTO wishlist (user_id, post_id, added_at) VALUES
(5, 1, '2026-04-20 10:15:00'),
(5, 2, '2026-04-20 10:20:00'),
(5, 3, '2026-04-21 11:30:00'),
(1, 4, '2026-04-22 09:45:00'),
(2, 1, '2026-04-22 14:00:00'),
(3, 5, '2026-04-23 16:20:00');

-- ── Demo Comments ─────────────────────────────────────────────────────────────────
INSERT IGNORE INTO comments (post_id, user_id, content, created_at) VALUES
(1, 5, 'Amazing place! Visited last month and the sunset was breathtaking. Highly recommended!', '2026-04-20 11:00:00'),
(1, 2, 'Best beach in Bangladesh. The seafood is also incredible. Don\'t miss the Inani Beach nearby.', '2026-04-20 15:30:00'),
(2, 5, 'Saw a Royal Bengal Tiger during the boat safari! Once in a lifetime experience.', '2026-04-21 10:45:00'),
(3, 1, 'The cloud view from Sajek is unreal. Felt like walking in heaven. Must visit during winter.', '2026-04-22 09:15:00'),
(4, 5, 'Seven-layer tea is a must try! The tea gardens are so peaceful and beautiful.', '2026-04-23 14:20:00'),
(5, 3, 'Rich history and well-preserved ruins. Great for history enthusiasts and photographers.', '2026-04-24 11:00:00'),
(1, 3, 'Pro tip: Visit during November-February for the best weather. Avoid monsoon season.', '2026-04-25 16:45:00');

-- ── Demo Cost Estimates ───────────────────────────────────────────────────────────
INSERT IGNORE INTO cost_estimates (post_id, base_cost, currency, last_updated) VALUES
(1, 5000.00, 'BDT', '2026-04-20 10:00:00'),
(2, 8000.00, 'BDT', '2026-04-20 10:00:00'),
(3, 6000.00, 'BDT', '2026-04-20 10:00:00'),
(4, 3000.00, 'BDT', '2026-04-20 10:00:00'),
(5, 2500.00, 'BDT', '2026-04-20 10:00:00');
