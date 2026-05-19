CREATE DATABASE IF NOT EXISTS travel_guide;
USE travel_guide;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(120) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','scout','user') NOT NULL DEFAULT 'user',
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  profile_picture VARCHAR(255),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  scout_id INT,
  title VARCHAR(150) NOT NULL,
  short_history TEXT,
  country VARCHAR(100),
  genre VARCHAR(50),
  cost_level ENUM('low','medium','high') DEFAULT 'low',
  travel_medium_info TEXT,
  status ENUM('pending','approved','rejected') DEFAULT 'approved',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (scout_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS post_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  scout_id INT,
  post_data JSON,
  requested_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  FOREIGN KEY (scout_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS wishlist (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  post_id INT,
  added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS comments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT,
  user_id INT,
  content TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cost_estimates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  post_id INT,
  base_cost DECIMAL(10,2),
  currency VARCHAR(10),
  last_updated DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
);

INSERT INTO users(name,email,password_hash,role,is_verified,created_at)
VALUES('Admin Student','admin@test.com','$2y$10$Pg14M.avMavEYww3QYdBEOvWj9GqmBZoePhuT97wM.rra1HlcH4Q2','admin',1,NOW());
-- password: admin12345
