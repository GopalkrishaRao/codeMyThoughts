-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 28, 2024 at 02:16 PM
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
-- Database: `cmt_ibn2`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `chapter_id` varchar(255) NOT NULL,
  `up_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `sl_no` int(11) NOT NULL,
  `chapter_id` varchar(255) NOT NULL,
  `event_id` varchar(100) DEFAULT NULL,
  `member_id` varchar(100) DEFAULT NULL,
  `attended_as` enum('member','substitute') DEFAULT NULL,
  `time_of_authentication` datetime DEFAULT NULL,
  `payment_status` enum('Paid','Due') DEFAULT NULL,
  `attendance_status` enum('Absent','Present') DEFAULT 'Absent',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`sl_no`, `chapter_id`, `event_id`, `member_id`, `attended_as`, `time_of_authentication`, `payment_status`, `attendance_status`, `updated_at`) VALUES
(1, 'IND-KA-0001', 'IND-KA-0001-0000000001', 'IND-KA-0001-001', NULL, NULL, 'Paid', 'Absent', '2024-08-28 12:13:55'),
(2, '', 'NAT-02', 'IND-KA-0001-001', 'member', '2024-08-28 15:30:34', 'Due', 'Absent', '2024-08-28 11:01:35'),
(3, '', 'KA-0001-0000000001', 'IND-KA-0001-001', NULL, NULL, 'Paid', 'Absent', '2024-08-28 12:13:53'),
(4, '', 'Global-01', 'IND-KA-0001-001', 'member', '2024-08-28 14:18:50', 'Paid', 'Absent', '2024-08-28 11:01:44'),
(5, '', 'KA-001-11', 'IND-KA-0001-001', 'member', '2024-08-28 14:02:06', 'Paid', 'Absent', '2024-08-28 11:01:49');

-- --------------------------------------------------------

--
-- Table structure for table `chapters`
--

CREATE TABLE `chapters` (
  `chapter_id` varchar(100) NOT NULL,
  `country` varchar(50) DEFAULT NULL,
  `state` varchar(50) DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `city_tier` enum('T1','T2','T3') DEFAULT NULL,
  `chapter_name` varchar(100) NOT NULL,
  `creation_date` datetime DEFAULT curdate(),
  `numb_id` int(11) NOT NULL,
  `statu` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`chapter_id`, `country`, `state`, `city`, `city_tier`, `chapter_name`, `creation_date`, `numb_id`, `statu`) VALUES
('IND-KA-0001', 'IND', 'KA', 'Sagar', 'T1', 'bussness king', '2024-08-12 00:00:00', 1, 'active'),
('IND-KL-0001', 'IND', 'KL', 'Aluva', 'T2', 'kerala', '2024-08-13 00:00:00', 1, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `eventcategory`
--

CREATE TABLE `eventcategory` (
  `sl.no` int(11) NOT NULL,
  `chapter_id` varchar(255) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `up_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventcategory`
--

INSERT INTO `eventcategory` (`sl.no`, `chapter_id`, `category_name`, `up_date`) VALUES
(1, 'IND-KA-0001', 'rtes', '2024-08-12 14:09:17'),
(2, 'IND-KL-0001', 'sfasd', '2024-08-13 06:32:12');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` varchar(100) NOT NULL,
  `chapter_id` varchar(255) DEFAULT NULL,
  `meeting_type` varchar(50) NOT NULL,
  `agenda` varchar(50) DEFAULT NULL,
  `fee` int(10) NOT NULL,
  `mandatory` varchar(10) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `latitude` decimal(9,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) NOT NULL,
  `scheduled_by` varchar(25) DEFAULT NULL,
  `numb_id` int(11) NOT NULL,
  `event_type` varchar(10) DEFAULT NULL,
  `mode` enum('online','offline') NOT NULL,
  `statu` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `chapter_id`, `meeting_type`, `agenda`, `fee`, `mandatory`, `description`, `location`, `latitude`, `longitude`, `start_date`, `end_date`, `start_time`, `end_time`, `venue`, `scheduled_by`, `numb_id`, `event_type`, `mode`, `statu`) VALUES
('IND-KA-0001-0000000001', 'IND-KA-0001', 'rtes', 'gfd', 900, 'yes', 'hgf', 'Majestic, Bengaluru, Karnataka, India', 12.9766637, 77.5712556, '2024-08-28', '2024-08-28', '14:45:00', '16:43:00', 'sadfse', 'super_admin', 1, 'chapter', 'online', 'active'),
('IND-KA-0001-0000000002', 'IND-KA-0001', 'Business Meeting', '', 100, 'yes', '', 'Yeshwanthpur, Bengaluru, Karnataka, India', 13.0250302, 77.5340242, '2024-08-29', '2024-08-21', '14:04:00', '14:04:00', 'easdf', 'chapter', 2, 'chapter', 'online', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `global_events`
--

CREATE TABLE `global_events` (
  `event_id` varchar(100) NOT NULL,
  `id` int(11) NOT NULL,
  `chapter_id` varchar(255) DEFAULT NULL,
  `meeting_type` varchar(50) NOT NULL,
  `agenda` varchar(50) DEFAULT NULL,
  `fee` int(10) NOT NULL,
  `mandatory` varchar(10) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `latitude` decimal(9,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) NOT NULL,
  `scheduled_by` varchar(25) DEFAULT NULL,
  `numb_id` int(11) NOT NULL,
  `event_type` varchar(10) DEFAULT NULL,
  `mode` enum('online','offline') NOT NULL,
  `statu` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `global_events`
--

INSERT INTO `global_events` (`event_id`, `id`, `chapter_id`, `meeting_type`, `agenda`, `fee`, `mandatory`, `description`, `location`, `latitude`, `longitude`, `start_date`, `end_date`, `start_time`, `end_time`, `venue`, `scheduled_by`, `numb_id`, `event_type`, `mode`, `statu`) VALUES
('Global-01', 1, 'IND-KA-0001', 'rtes', 'gfd', 900, 'yes', 'hgf', 'Majestic, Bengaluru, Karnataka, India', 12.9766637, 77.5712556, '2024-08-28', '2024-08-28', '19:45:00', '19:43:00', 'sadfse', 'super_admin', 1, 'chapter', 'online', 'active'),
('Global-02', 2, 'IND-KA-0001', 'Business Meeting', '', 100, 'yes', '', 'Yeshwanthpur, Bengaluru, Karnataka, India', 13.0250302, 77.5340242, '2024-08-27', '2024-08-28', '12:04:00', '14:04:00', 'easdf', 'chapter', 2, 'chapter', 'online', 'active'),
('NAT-IND-0000000001', 3, NULL, '', 'sfjk', 300, 'yes', 'kadjsl', 'Nagarjuna Degree College, Bengaluru, Karnataka, India', 13.1162406, 77.5620315, '2024-08-13', '2024-08-13', '12:03:00', '12:04:00', 'value', 'super_admin', 1, 'national', 'online', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `sl.no` int(11) NOT NULL,
  `member_id` varchar(100) NOT NULL,
  `chapter_id` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `alt_email` varchar(50) DEFAULT NULL,
  `ph_number` varchar(15) DEFAULT NULL,
  `alt_phone` int(11) DEFAULT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `blood_group` varchar(11) DEFAULT NULL,
  `bio` varchar(255) DEFAULT NULL,
  `fee` int(10) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `anniversary` varchar(100) DEFAULT NULL,
  `husband_wife_name` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `numb_id` int(11) NOT NULL,
  `statu` varchar(10) NOT NULL,
  `token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`sl.no`, `member_id`, `chapter_id`, `role`, `created_by`, `user_type`, `name`, `email`, `alt_email`, `ph_number`, `alt_phone`, `dob`, `blood_group`, `bio`, `fee`, `profile_pic`, `anniversary`, `husband_wife_name`, `gender`, `business_type`, `industry`, `sector`, `numb_id`, `statu`, `token`) VALUES
(3, 'IND-KA-0001-001', 'IND-KA-0001', 'admin', 'super admin', NULL, 'adsf', 'gkhr94@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJ1MJilJrCVddG53ydiHSPUd9WLYfy3wtgK-0-SM3MG51V4cKU=s96-c', NULL, NULL, NULL, 'asdf', 'afds', 'asd', 1, 'active', 'eyJhbGciOiJSUzI1NiIsImtpZCI6IjQ1MjljNDA5Zjc3YTEwNmZiNjdlZTFhODVkMTY4ZmQyY2ZiN2MwYjciLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI3NzMwMjg4OTgwNTAtbWMwcWxkNWwxM2kwZ2I0YjFkdXZ0dW8xaGtvNWs1ZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI3NzMwMjg4OTgwNTAtbWMwcWxkNWwxM2kwZ2I0YjFkdXZ0dW8xaGtvNWs1ZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTQ2NjIwNzI1NjUxOTk5OTI4MTEiLCJlbWFpbCI6ImdraHI5NEBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6Ik1uWU9SRHJ2bXR3Vl9SYnBSRnFWRUEiLCJuYW1lIjoiR0tIUiBSYW8iLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFNSmlsSnJDVmRkRzUzeWRpSFNQVWQ5V0xZZnkzd3RnSy0wLVNNM01HNTFWNGNLVT1zOTYtYyIsImdpdmVuX25hbWUiOiJHS0hSIiwiZmFtaWx5X25hbWUiOiJSYW8iLCJpYXQiOjE3MjM1NDA1MjUsImV4cCI6MTcyMzU0NDEyNX0.cNxigvM8oYcwCNzf5JKVHr3f3uH0s80RLbV9ztxljU-4QAc_13xNFuZUjAvhXFoAhIKgZeFh6SQ-KwHuOs5lBO_qMB7pvdqRqdGNJA01aym3MmP3SP0RWg8aXYkb73C9f8xHNZ_xeF0BvELNeYK7soNzYupJREBYeHmVX5d2ZGwGW1ti3oQPCAqub5qFbbYTEFTu7gwswVLIy7r1uTYUd9H0ryiuq-makxGnUr1fRQQGZkPtssOBkB1tTf1EuZG8FaCFeFWxNPHNQH4NVG2co2CQ1DdPxCz413I-1Z6fDsjidjIjpp1ppxmiJyzt0s88aO8ZxUkzLO4wRCjJnVpT-g');

-- --------------------------------------------------------

--
-- Table structure for table `national_events`
--

CREATE TABLE `national_events` (
  `id` int(11) NOT NULL,
  `event_id` varchar(100) NOT NULL,
  `chapter_id` varchar(255) DEFAULT NULL,
  `meeting_type` varchar(50) NOT NULL,
  `agenda` varchar(50) DEFAULT NULL,
  `fee` int(10) NOT NULL,
  `mandatory` varchar(10) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `latitude` decimal(9,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) NOT NULL,
  `scheduled_by` varchar(25) DEFAULT NULL,
  `numb_id` int(11) NOT NULL,
  `event_type` varchar(10) DEFAULT NULL,
  `mode` enum('online','offline') NOT NULL,
  `statu` varchar(10) NOT NULL,
  `country` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `national_events`
--

INSERT INTO `national_events` (`id`, `event_id`, `chapter_id`, `meeting_type`, `agenda`, `fee`, `mandatory`, `description`, `location`, `latitude`, `longitude`, `start_date`, `end_date`, `start_time`, `end_time`, `venue`, `scheduled_by`, `numb_id`, `event_type`, `mode`, `statu`, `country`) VALUES
(1, 'NAT-01', 'IND-KA-0001', 'rtes', 'gfd', 900, 'yes', 'hgf', 'Majestic, Bengaluru, Karnataka, India', 13.0039365, 77.5495811, '2024-08-28', '2024-08-28', '19:45:00', '19:43:00', 'sadfse', 'super_admin', 1, 'chapter', 'online', 'active', 'PAK'),
(2, 'NAT-02', 'IND-KA-0001', 'Business Meeting', '', 100, 'yes', '', 'Yeshwanthpur, Bengaluru, Karnataka, India', 13.0250302, 77.5340242, '2024-08-28', '2024-08-28', '14:04:00', '14:04:00', 'easdf', 'chapter', 2, 'chapter', 'online', 'active', 'IND');

-- --------------------------------------------------------

--
-- Table structure for table `state_events`
--

CREATE TABLE `state_events` (
  `event_id` varchar(100) NOT NULL,
  `id` int(11) NOT NULL,
  `chapter_id` varchar(255) DEFAULT NULL,
  `meeting_type` varchar(50) NOT NULL,
  `agenda` varchar(50) DEFAULT NULL,
  `fee` int(10) NOT NULL,
  `mandatory` varchar(10) NOT NULL,
  `description` varchar(250) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `latitude` decimal(9,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) NOT NULL,
  `scheduled_by` varchar(25) DEFAULT NULL,
  `numb_id` int(11) NOT NULL,
  `event_type` varchar(10) DEFAULT NULL,
  `mode` enum('online','offline') NOT NULL,
  `statu` varchar(10) NOT NULL,
  `country` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `state_events`
--

INSERT INTO `state_events` (`event_id`, `id`, `chapter_id`, `meeting_type`, `agenda`, `fee`, `mandatory`, `description`, `location`, `latitude`, `longitude`, `start_date`, `end_date`, `start_time`, `end_time`, `venue`, `scheduled_by`, `numb_id`, `event_type`, `mode`, `statu`, `country`, `state`) VALUES
('KA-0001-0000000001', 1, 'IND-KA-0001', 'rtes', 'gfd', 900, 'yes', 'hgf', 'Majestic, Bengaluru, Karnataka, India', 12.9766637, 77.5712556, '2024-08-28', '2024-08-28', '17:45:00', '19:43:00', 'sadfse', 'super_admin', 1, 'chapter', 'online', 'active', 'IND', 'KA'),
('AP-001-11', 2, 'ka-ka-o1', 'online', 'safd', 100, '', 'asdfklajsdf', 'Sagara', 12.9766637, 77.5712556, '2024-08-28', '2024-08-28', NULL, NULL, 'sagara', 'admin', 2, NULL, 'online', '', 'IND', 'AP');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`sl_no`),
  ADD KEY `attendance_ibfk_1` (`event_id`),
  ADD KEY `attendance_ibfk_3` (`member_id`);

--
-- Indexes for table `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`chapter_id`);

--
-- Indexes for table `eventcategory`
--
ALTER TABLE `eventcategory`
  ADD PRIMARY KEY (`sl.no`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `global_events`
--
ALTER TABLE `global_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`sl.no`),
  ADD KEY `member_ibfk_1` (`chapter_id`);

--
-- Indexes for table `national_events`
--
ALTER TABLE `national_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `state_events`
--
ALTER TABLE `state_events`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `sl_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `eventcategory`
--
ALTER TABLE `eventcategory`
  MODIFY `sl.no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `global_events`
--
ALTER TABLE `global_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `sl.no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `national_events`
--
ALTER TABLE `national_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `state_events`
--
ALTER TABLE `state_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`chapter_id`);

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `member_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`chapter_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
