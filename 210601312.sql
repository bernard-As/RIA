-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 12:50 PM
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
-- Database: `210601312`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `name`, `description`, `user_id`) VALUES
(1, 'ded', 'eded', 6),
(2, 'ed', 'eded', 6),
(4, 'eded', 'eded', 6),
(5, 'weded', 'eded', 6),
(6, 'ftrfr', 'vfv', 6);

-- --------------------------------------------------------

--
-- Table structure for table `item_photos`
--

CREATE TABLE `item_photos` (
  `id` int(11) NOT NULL,
  `item_id` int(11) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item_photos`
--

INSERT INTO `item_photos` (`id`, `item_id`, `photo`) VALUES
(1, 1, '673a4f6d19cbb9.09902160.png'),
(2, 1, '673a4f6d19f503.68449302.png'),
(3, 2, '673a4f7dda4364.95634349.png'),
(4, 2, '673a4f7dda7187.45395920.png'),
(6, 4, '673a4fb83d0d07.35991003.jpg'),
(7, 4, '673a4fb83d5482.29211543.jpg'),
(8, 5, '673a52a08b9898.05588856.jpg'),
(9, 6, '673a543f254946.98568257.jpg'),
(17, 4, '673a623fa2da99.93694013.jpg'),
(18, 4, '673a62433d1748.70061476.jpg'),
(19, 4, '673a6245d78835.69983367.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `firstname`, `lastname`, `email`, `profile_picture`) VALUES
(6, '210601312@rdu.edu.tr', '$2y$10$GFrVFn0hpLzf8wCcJr0TmuykIn8OXWE4GV6UabQHIcNvm1sAkttXC', 'admin', '2024-11-17 17:57:22', 'bernard', 'Bernard', '210601312@rdu.edu.tr', 'profile_673b0b0aaa8d64.45774593.png'),
(7, '210601313@rdu.edu.tr', '$2y$10$MZ2zAIBapzrzYXaoedq1cuEt5ugSPsRtEvZUBLcRm9uDIZ5G5QNim', 'admin', '2024-11-18 10:47:01', '210601313@rdu.edu.tr', '210601313@rdu.edu.tr', '210601313@rdu.edu.tr', NULL),
(8, '21060131233@rdu.edu.tr', '$2y$10$CxS68QpuIEXXbB7MpyhrTeJTfNqRA8Gzsywrqa6386Y5PsWgUbRWS', 'admin', '2024-11-18 11:44:12', 'Ber', 'er', '21060131233@rdu.edu.tr', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `item_photos`
--
ALTER TABLE `item_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `item_photos`
--
ALTER TABLE `item_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `item_photos`
--
ALTER TABLE `item_photos`
  ADD CONSTRAINT `item_photos_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
