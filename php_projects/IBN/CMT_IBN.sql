-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 02, 2024 at 09:36 AM
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
-- Database: `CMT_IBN`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `sl_no` int(11) NOT NULL,
  `event_id` varchar(100) DEFAULT NULL,
  `member_id` varchar(100) DEFAULT NULL,
  `attended_as` enum('member','substitute') DEFAULT NULL,
  `time_of_authentication` datetime DEFAULT NULL,
  `payment_status` enum('Paid','Due') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`sl_no`, `event_id`, `member_id`, `attended_as`, `time_of_authentication`, `payment_status`) VALUES
(1, 'AFG/BDG/0001/0000000001', 'AFG/BDG/0001/002', 'substitute', '2024-08-01 19:47:39', 'Paid'),
(2, 'NAT/IND/0000000001', 'AFG/BDG/0001/002', 'member', '2024-08-02 11:45:56', 'Paid'),
(3, 'AFG/BDG/0001/0000000001', 'AFG/BDG/0001/001', NULL, NULL, 'Due'),
(4, 'NAT/IND/0000000001', 'AFG/BDG/0001/001', NULL, NULL, 'Due');

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
  `area` varchar(50) DEFAULT NULL,
  `chapter_name` varchar(100) NOT NULL,
  `creation_date` datetime DEFAULT curdate(),
  `numb_id` int(11) NOT NULL,
  `admin` varchar(100) NOT NULL,
  `chapter_status` enum('active','inactive') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chapters`
--

INSERT INTO `chapters` (`chapter_id`, `country`, `state`, `city`, `city_tier`, `area`, `chapter_name`, `creation_date`, `numb_id`, `admin`, `chapter_status`) VALUES
('AFG/BDG/0001', 'AFG', 'BDG', 'Ghormach', 'T2', 'dfs', 'sdfa', '2024-07-29 00:00:00', 1, 'dsaf', 'active'),
('AFG/BDG/0002', 'AFG', 'BDG', 'Ghormach', 'T1', 'asdf', 'sdfa', '2024-07-30 00:00:00', 2, 'sdfas', 'active'),
('AFG/BDS/0001', 'AFG', 'BDS', 'Ashkāsham', 'T2', 'sdfgh', 'dg', '2024-07-28 00:00:00', 1, 'dsfg', 'inactive'),
('AND/07/0001', 'AND', '07', 'Andorra la Vella', 'T1', 'asdf', 'asdfadsf', '2024-07-28 00:00:00', 1, 'sdfa', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` varchar(100) NOT NULL,
  `chapter_id` varchar(255) DEFAULT NULL,
  `meeting_type` varchar(50) NOT NULL,
  `agenda` varchar(50) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `venue` varchar(100) NOT NULL,
  `scheduled_by` varchar(25) DEFAULT NULL,
  `numb_id` int(11) NOT NULL,
  `event_type` varchar(10) DEFAULT NULL,
  `mode` enum('online','offline') NOT NULL,
  `latitude` decimal(9,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `chapter_id`, `meeting_type`, `agenda`, `description`, `location`, `start_date`, `end_date`, `start_time`, `end_time`, `venue`, `scheduled_by`, `numb_id`, `event_type`, `mode`, `latitude`, `longitude`) VALUES
('AFG/BDG/0001/0000000001', 'AFG/BDG/0001', 'casual', 'adsf', 'sdf', 'sdf', '2024-08-02', '2024-08-01', '14:06:00', '19:01:00', 'dsfa', 'super_admin', 1, 'chapter', 'offline', 13.0041514, 77.5522484),
('NAT/IND/0000000001', NULL, 'business', 'adsf', 'asdf', 'sdf', '2024-08-02', '2024-08-01', '14:20:00', '12:30:00', 'asdf', 'super_admin', 1, 'national', 'online', 13.0041514, 77.5522484);

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$qSkvYnKLhC22SdrkWd7e2.L9Hlm7dp9NrzdE9sdl3uMsynuZpIt5u');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `member_id` varchar(100) NOT NULL,
  `chapter_id` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `alt_email` varchar(50) DEFAULT NULL,
  `ph_number` varchar(15) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `anniversary` date DEFAULT NULL,
  `husband_wife_name` varchar(50) DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `numb_id` int(11) NOT NULL,
  `token` text DEFAULT NULL,
  `member_status` enum('active','inactive') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`member_id`, `chapter_id`, `role`, `user_type`, `name`, `email`, `alt_email`, `ph_number`, `dob`, `profile_pic`, `anniversary`, `husband_wife_name`, `gender`, `business_type`, `industry`, `sector`, `numb_id`, `token`, `member_status`) VALUES
('AFG/BDG/0001/001', 'AFG/BDG/0001', 'admin', 'vice_president', 'gdfs', 'h.r.gopalkrishna@gmail.com', NULL, NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocJT8xjpRHe8YNCQe3JbNrD47WdhoEktmL5zbtBrvHtQ8NAk6ik=s96-c', NULL, NULL, 'Male', 'sadf', 'sadf', 'fsad', 1, 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImUyNmQ5MTdiMWZlOGRlMTMzODJhYTdjYzlhMWQ2ZTkzMjYyZjMzZTIiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI3NzMwMjg4OTgwNTAtbWMwcWxkNWwxM2kwZ2I0YjFkdXZ0dW8xaGtvNWs1ZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI3NzMwMjg4OTgwNTAtbWMwcWxkNWwxM2kwZ2I0YjFkdXZ0dW8xaGtvNWs1ZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMDM4OTgzNDcxMTM4MDY0MzIxNDYiLCJlbWFpbCI6Imguci5nb3BhbGtyaXNobmFAZ21haWwuY29tIiwiZW1haWxfdmVyaWZpZWQiOnRydWUsImF0X2hhc2giOiJoNDl5Mml6NlppYUFJeUsxdFl2R1p3IiwibmFtZSI6IkdvcGFsa3Jpc2huYSBSYW8iLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSlQ4eGpwUkhlOFlOQ1FlM0piTnJENDdXZGhvRWt0bUw1emJ0QnJ2SHRROE5BazZpaz1zOTYtYyIsImdpdmVuX25hbWUiOiJHb3BhbGtyaXNobmEiLCJmYW1pbHlfbmFtZSI6IlJhbyIsImlhdCI6MTcyMjM0NzI4NSwiZXhwIjoxNzIyMzUwODg1fQ.ie8-L3KzJx6yTzhtKKMwZHmM-Vg93GtH5pRpU_ex-KYzywK8En5qzn1thRR4rqA9hC34d7i4z79QEF_rYUMS01ETOD_manjJm_o4elcWYLK9WO-scR2mk3G-qyu7SqxZ4PGW_6hPUZ4lX8tdnScU8G3YKZWJ_U_eeovK9JFjG075gULG9tuEqrdAzn6SVKtKuZaD2PbY_5uSTt5IHtaOCEu4v1rv1qIPcth3ffiBrKJy1XwcygY_kN7xleZ2ifJSiJQXjkcREmuBEbR9gEQpMrC_ezrW_4WMyjFonP45pEhd-o0EAbU0vO-vyrzQUb0XDNCFMtl7ny4cpBvQHrsFmA', 'active'),
('AFG/BDG/0001/002', 'AFG/BDG/0001', 'member', NULL, 'sdfa', 'gkhr94@gmail.com', NULL, NULL, NULL, 'https://lh3.googleusercontent.com/a/ACg8ocKbNdQxXsetSkY_oIxhs3dzzquoVSaeiYScFJUHXaea802sOoU=s96-c', NULL, NULL, NULL, 'sdfa', 'asdf', 'sadf', 2, 'eyJhbGciOiJSUzI1NiIsImtpZCI6ImUyNmQ5MTdiMWZlOGRlMTMzODJhYTdjYzlhMWQ2ZTkzMjYyZjMzZTIiLCJ0eXAiOiJKV1QifQ.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmdvb2dsZS5jb20iLCJhenAiOiI3NzMwMjg4OTgwNTAtbWMwcWxkNWwxM2kwZ2I0YjFkdXZ0dW8xaGtvNWs1ZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJhdWQiOiI3NzMwMjg4OTgwNTAtbWMwcWxkNWwxM2kwZ2I0YjFkdXZ0dW8xaGtvNWs1ZDMuYXBwcy5nb29nbGV1c2VyY29udGVudC5jb20iLCJzdWIiOiIxMTQ2NjIwNzI1NjUxOTk5OTI4MTEiLCJlbWFpbCI6ImdraHI5NEBnbWFpbC5jb20iLCJlbWFpbF92ZXJpZmllZCI6dHJ1ZSwiYXRfaGFzaCI6IlUzNGIwYTJCSmlvMjRQNU5ybEktenciLCJuYW1lIjoiR0tIUiBSYW8iLCJwaWN0dXJlIjoiaHR0cHM6Ly9saDMuZ29vZ2xldXNlcmNvbnRlbnQuY29tL2EvQUNnOG9jSjFNSmlsSnJDVmRkRzUzeWRpSFNQVWQ5V0xZZnkzd3RnSy0wLVNNM01HNTFWNGNLVT1zOTYtYyIsImdpdmVuX25hbWUiOiJHS0hSIiwiZmFtaWx5X25hbWUiOiJSYW8iLCJpYXQiOjE3MjI0MzQ3MTksImV4cCI6MTcyMjQzODMxOX0.e_kV3WAbyOEhylGWXHMz7lwYmYIzceBnbSZUEcH_du9Z2yDIxazK063CY9k1i4k3E7FMDslfBSVCyhwDrn8JQjMh9Is3ac4MRo_3W5NAs_-mTIOyXTb0z1zGyOJ4sWdD7Z_VBHDbnq6WnZv2G_xdTd36qFAeRyPp3WJ1bBm6NdhR9kh9rlaUMQ8qgkGUfYsSJohNSSjHqXkVg4oa7HKP-AefwNeqST9XVdEGvzyQWKx35Yyxla1peOooSOY0ljIaNJE0J24lrE8YepMni2cpdRUge4dXsyjhcKjdQNV_ujGKvSg2GGDHChgTTjrROJwxBziB7ydkpEkcob1pvrlAvw', 'active');

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`chapter_id`),
  ADD KEY `FK_Chapters_Member` (`admin`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `ph_number` (`ph_number`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `sl_no` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `attendance_ibfk_3` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`);

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
