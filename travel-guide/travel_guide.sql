-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 11:43 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `travel_guide`
--

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `cost_level` varchar(50) DEFAULT NULL,
  `short_history` text DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title`, `country`, `genre`, `cost_level`, `short_history`, `status`) VALUES
(1, 'Coxs Bazar Beach', 'Bangladesh', NULL, 'low', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('admin','scout','user') DEFAULT NULL,
  `is_verified` tinyint(4) DEFAULT 1,
  `profile_picture` varchar(255) DEFAULT NULL,
  `remember_token` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `is_verified`, `profile_picture`, `remember_token`, `created_at`, `profile_image`) VALUES
(1, 'Raiyan Chowdhury', 'lordraiyan7@gmail.com', '$2y$10$0ekpd17cNVBZsufh2cW7yOIpoCIO3ua9B5CoVjAB6/gY6sPYbpBMW', 'user', 1, NULL, '537b0858a5522a6cd4d4ab418b81ef58a1bdfa9357d5e34f323481a421155630', '2026-05-18 08:51:53', '1779138815_483865000_3639973826297294_9200559865506841194_n.jpg'),
(2, 'admin', 'admin@12', '$2y$10$u1dxKPSmmyAeOCZ6C23AkOBpxeHVhOSv33OWlgw7FROtmi8PEa35K', 'admin', 1, NULL, NULL, '2026-05-18 09:59:10', NULL),
(3, 'Rafsan', 'rafsan@123.com', '$2y$10$GqzZ9rCeiFORt.jks.BuNerlZITKKPwIkvRbFQoBq3.yb5fqS2T0m', 'user', 0, NULL, NULL, '2026-05-18 20:41:44', NULL),
(4, 'er', 'ew@das.com', '$2y$10$pyJTTk358hGWSELWlQXwVuuzRB5FJtPeFCCKTcM1UrNdG7D91G1e2', 'user', 0, NULL, NULL, '2026-05-18 20:48:53', NULL),
(5, 'tulon', 'tulon@gamil.com', '$2y$10$7o5GaF7h57.tGG9rL291CuW3c84d6qY29HKDZE9y6KBsAL7baqfJa', 'scout', 0, NULL, NULL, '2026-05-18 21:30:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `post_id` int(11) DEFAULT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `post_id`, `added_at`) VALUES
(1, 1, 1, '2026-05-18 11:35:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_post` (`user_id`,`post_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
