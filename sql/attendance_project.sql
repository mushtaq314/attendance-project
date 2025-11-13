-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 13, 2025 at 09:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `attendance_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('login','logout','break_start','break_end','location') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `provider` varchar(50) DEFAULT NULL,
  `accuracy` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `emp_notifications`
--

CREATE TABLE `emp_notifications` (
  `id` int(11) NOT NULL,
  `emp_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `face_descriptors`
--

CREATE TABLE `face_descriptors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `descriptor_json` mediumtext NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` text DEFAULT NULL,
  `visible_to` enum('all','employees','admins') DEFAULT 'all',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `user_id`, `title`, `body`, `visible_to`, `created_at`) VALUES
(1, 1, 'Team Collaboration Tips', 'Communicate clearly with your team members and update task notes regularly. Collaboration improves project efficiency and reduces confusion.', 'all', '2025-11-12 17:55:11'),
(2, 8, 'Monthly Performance Review', 'Managers will review employee performance on the last Friday of every month. Please complete all pending tasks before the review date.', 'all', '2025-11-12 21:31:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `role` enum('employee','admin') DEFAULT 'employee',
  `approved` tinyint(1) DEFAULT 0,
  `status` enum('pending','approved','rejected','active') DEFAULT 'pending',
  `twofa_secret` varchar(255) DEFAULT NULL,
  `face_descriptor` mediumtext DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `role`, `approved`, `status`, `twofa_secret`, `face_descriptor`, `created_at`) VALUES
(1, 'System Administrator', 'admin@example.com', '$2y$10$YBnRh0gzoDnjN6HbgFx9JeiA2RPFF8xOuNJNEbnBj3nibEnpKOnCG', NULL, 'admin', 1, 'active', NULL, NULL, '2025-11-12 00:21:47'),
(2, 'Mushtaq S', 'mushtaqshaikh314@gmail.com', '$2y$10$9xf7JY0giZhZRc3IAcOOMey3JYqYODnPd15PVxrBjCIqaTMnpgIfi', NULL, 'employee', 1, 'pending', NULL, NULL, '2025-11-12 00:27:25'),
(3, 'Shaikh M', 'test@test.com', '$2y$10$XbhnQyyKOUcIxRZeWvA7DuYUHjbr9314Mw5iU1B.1MNpmIvZvdo5S', NULL, 'employee', 1, 'approved', NULL, NULL, '2025-11-12 17:56:39'),
(6, 'Abrar S', 'abc@abc.com', NULL, NULL, 'employee', 1, 'pending', NULL, NULL, '2025-11-12 18:57:01'),
(8, 'Mushtaq S', 'admin@123.com', '$2y$10$bxoGq6netKq3B7gdnxiQUuDt3iuI4eu8HJd/7DKBUfcIpKKk1Y1nK', NULL, 'admin', 1, 'approved', NULL, NULL, '2025-11-12 19:39:08'),
(9, 'aa', 'a@123.com', NULL, NULL, 'employee', 0, 'pending', NULL, NULL, '2025-11-13 16:11:47'),
(10, 'aa', 'aa22@qw.com', NULL, NULL, 'employee', 0, 'pending', NULL, NULL, '2025-11-13 16:14:42'),
(11, 'sa', 'sa@we.com', NULL, NULL, 'employee', 0, 'pending', NULL, NULL, '2025-11-13 16:32:55'),
(12, 'as', 'ax@as.com', NULL, NULL, 'employee', 0, 'pending', NULL, NULL, '2025-11-13 16:43:09'),
(13, 'Mushtaq S', 'creatives@ondirect.in', NULL, NULL, 'employee', 0, 'pending', NULL, NULL, '2025-11-13 18:56:31');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `emp_notifications`
--
ALTER TABLE `emp_notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emp_id` (`emp_id`);

--
-- Indexes for table `face_descriptors`
--
ALTER TABLE `face_descriptors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `emp_notifications`
--
ALTER TABLE `emp_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `face_descriptors`
--
ALTER TABLE `face_descriptors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emp_notifications`
--
ALTER TABLE `emp_notifications`
  ADD CONSTRAINT `emp_notifications_ibfk_1` FOREIGN KEY (`emp_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `face_descriptors`
--
ALTER TABLE `face_descriptors`
  ADD CONSTRAINT `face_descriptors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
