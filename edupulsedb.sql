-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2025 at 06:00 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `edupulsedb`
--
CREATE DATABASE IF NOT EXISTS `edupulsedb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `edupulsedb`;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

CREATE TABLE `schools` (
  `school_id` int(11) NOT NULL,
  `edupulse_id` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` enum('Primary','Secondary') NOT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `motto` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(50) DEFAULT NULL,
  `established_year` year(4) DEFAULT NULL,
  `accreditation` text DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`school_id`, `edupulse_id`, `name`, `level`, `logo_url`, `address`, `latitude`, `longitude`, `motto`, `contact_email`, `contact_phone`, `established_year`, `accreditation`, `facilities`, `created_at`) VALUES
(1, 'SJSS1234', 'St. John\'s SS, Kampala', 'Secondary', 'Uploads/logos/edupulse_logo.png', 'Kampala, Uganda', 0.34760000, 32.58250000, 'Excellence and Integrity', 'contact@stjohns.ac.ug', '+256701234567', 1985, 'Ministry of Education and Sports, Uganda', 'Library, Science Labs, Computer Lab, Sports Field', '2025-08-02 04:00:00'),
(2, 'KAMP1234', 'Kampala PS', 'Primary', 'Uploads/logos/edupulse_logo.png', 'Kampala, Uganda', 0.29500000, 32.60150000, 'Foundation for the Future', 'info@kampalaps.ac.ug', '+256771234567', 1992, 'Ministry of Education and Sports, Uganda', 'Playground, Library, Dining Hall', '2025-08-02 04:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `edupulse_id` varchar(20) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `role` enum('Superadmin','Headteacher','Deputy Headteacher','DOS','Bursar','Teacher','Student','Parent') NOT NULL,
  `profile_photo_url` varchar(255) DEFAULT 'assets/images/placeholder.png',
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `edupulse_id`, `school_id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `role`, `profile_photo_url`, `status`, `last_login`, `created_at`) VALUES
(1, 'SUPER001', NULL, 'Super', 'Admin', 'superadmin@edupulse.com', '+256700000000', '$2y$10$N9.h3g/KIo4.1.Y.5.1.2uJ.Z.d.U.c.W.e.F.G.H.I.J.K.L.M.N.O', 'Superadmin', 'assets/images/placeholder.png', 'active', NULL, '2025-08-02 04:00:00'),
(2, 'HTSJSS001', 1, 'John', 'Doe', 'headteacher.sjss@edupulse.com', '+256701111111', '$2y$10$N9.h3g/KIo4.1.Y.5.1.2uJ.Z.d.U.c.W.e.F.G.H.I.J.K.L.M.N.O', 'Headteacher', 'assets/images/placeholder.png', 'active', NULL, '2025-08-02 04:00:00'),
(3, 'TEACH001', 1, 'Jane', 'Smith', NULL, '+256702222222', NULL, 'Teacher', 'assets/images/placeholder.png', 'active', NULL, '2025-08-02 04:00:00'),
(4, 'STUD001', 1, 'Peter', 'Jones', NULL, NULL, NULL, 'Student', 'assets/images/placeholder.png', 'active', NULL, '2025-08-02 04:00:00'),
(5, 'PARENT001', 2, 'Mary', 'Jane', NULL, '+256773333333', NULL, 'Parent', 'assets/images/placeholder.png', 'active', NULL, '2025-08-02 04:00:00');

--
-- Table structure for table `licenses`
--

CREATE TABLE `licenses` (
  `license_id` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `license_key` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `status` enum('active','expired','inactive') NOT NULL DEFAULT 'inactive',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `licenses`
--

INSERT INTO `licenses` (`license_id`, `school_id`, `license_key`, `expiry_date`, `status`, `created_at`) VALUES
(1, 1, 'SJSS-2025-ABCD-1234', '2026-08-01', 'active', '2025-08-02 04:00:00'),
(2, 2, 'KAMP-2025-EFGH-5678', '2024-08-01', 'expired', '2025-08-02 04:00:00');


--
-- Indexes for dumped tables
--

--
-- Indexes for table `schools`
--
ALTER TABLE `schools`
  ADD PRIMARY KEY (`school_id`),
  ADD UNIQUE KEY `edupulse_id` (`edupulse_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `edupulse_id` (`edupulse_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `school_id` (`school_id`);

--
-- Indexes for table `licenses`
--
ALTER TABLE `licenses`
  ADD PRIMARY KEY (`license_id`),
  ADD UNIQUE KEY `license_key` (`license_key`),
  ADD KEY `school_id` (`school_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `schools`
--
ALTER TABLE `schools`
  MODIFY `school_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `licenses`
--
ALTER TABLE `licenses`
  MODIFY `license_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE SET NULL;

--
-- Constraints for table `licenses`
--
ALTER TABLE `licenses`
  ADD CONSTRAINT `licenses_ibfk_1` FOREIGN KEY (`school_id`) REFERENCES `schools` (`school_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
