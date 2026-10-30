-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 03:15 PM
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
-- Database: `visitor_pass`
--

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','Security') NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`id`, `name`, `email`, `password`, `role`, `active`) VALUES
(1, 'Administrator', 'admin@vpass.com', '$2y$10$/Sqf1PcM3918bYSj5GCi/eXbdKJDUBTLdvUCzqbvZSnmsrVG34PaK', 'Admin', 1),
(2, 'Gate Guard', 'guard@vpass.com', '$2y$10$XFTx4cCokWBCPeVBytmW6Oa492BURFwr5VRFcL5ZX6LqbxW16vCca', 'Security', 1);

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `nic` varchar(20) NOT NULL,
  `purpose` varchar(200) NOT NULL,
  `host` varchar(120) NOT NULL,
  `pass_code` char(32) NOT NULL,
  `status` enum('Active','Used','Expired') NOT NULL DEFAULT 'Active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `name`, `phone`, `email`, `nic`, `purpose`, `host`, `pass_code`, `status`, `created_by`, `created_at`) VALUES
(1, 'Amal Mujeeb', '0711180777', 'amalmujeeb2003@gmail.com', '200385113700', 'meeting', 'shimla', 'f365a0fc9360f47479b8e96056446d00', 'Used', 2, '2026-09-22 03:44:20'),
(2, 'shim', '0787780826', 'shimlafathima2004@gmail.com', '200483501587', 'meeting', 'poochi', 'd35e12f6b7294810a5f5e67655a3e9f2', 'Active', 2, '2026-09-23 03:14:58'),
(3, 'Automated Test Visitor', '0771234567', 'test@example.com', '999753867V', 'System smoke test', 'Testing Department', 'be3ddc3a85989cd01e9cb73fb93a43b9', 'Active', 1, '2026-09-23 12:46:02');

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `id` int(11) NOT NULL,
  `visitor_id` int(11) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL,
  `entry_method` enum('qr','manual') NOT NULL,
  `exit_method` enum('qr','manual') DEFAULT NULL,
  `entry_by` int(11) NOT NULL,
  `exit_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`id`, `visitor_id`, `entry_time`, `exit_time`, `entry_method`, `exit_method`, `entry_by`, `exit_by`) VALUES
(1, 1, '2026-09-22 09:15:27', '2026-09-22 09:19:31', 'qr', 'qr', 2, 2),
(2, 2, '2026-09-23 08:47:32', NULL, 'manual', NULL, 2, NULL),
(5, 3, '2026-09-23 18:20:22', NULL, 'qr', NULL, 1, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nic` (`nic`),
  ADD UNIQUE KEY `pass_code` (`pass_code`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_id` (`visitor_id`),
  ADD KEY `entry_by` (`entry_by`),
  ADD KEY `exit_by` (`exit_by`),
  ADD KEY `entry_time` (`entry_time`),
  ADD KEY `exit_time` (`exit_time`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `visitors`
--
ALTER TABLE `visitors`
  ADD CONSTRAINT `visitors_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `staff` (`id`);

--
-- Constraints for table `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`),
  ADD CONSTRAINT `visits_ibfk_2` FOREIGN KEY (`entry_by`) REFERENCES `staff` (`id`),
  ADD CONSTRAINT `visits_ibfk_3` FOREIGN KEY (`exit_by`) REFERENCES `staff` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
