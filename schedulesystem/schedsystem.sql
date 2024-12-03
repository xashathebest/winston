-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:5222
-- Generation Time: Dec 01, 2024 at 03:23 PM
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
-- Database: `schedsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_description` text DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`id`, `course_code`, `course_description`, `department_id`) VALUES
(18, 'BSCS', 'Bachelor Of Science In Computer Science', 13),
(19, 'BSIT', 'Bachelor Of Science In Information Technology', 13),
(20, 'BSCE', 'Bachelor Of Science In Computer Engineering', 14),
(21, 'BSCivE', 'Bachelor OF Science In Civil Engineering', 14);

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `department_name`) VALUES
(13, 'College Of Computing Studies'),
(14, 'College Of Engineering');

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `room` varchar(50) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `teacher` int(100) DEFAULT NULL,
  `day` varchar(9) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedule`
--

INSERT INTO `schedule` (`id`, `student_id`, `section_id`, `room`, `subject`, `teacher`, `day`, `start_time`, `end_time`) VALUES
(1, NULL, 15, 'LR2', 'Website Development 101', 27, 'monday', '07:00:00', '09:00:00'),
(2, 29, 15, 'LR3', 'Information Management 1', 27, 'tuesday', '07:00:00', '10:00:00'),
(3, NULL, 16, 'LR3', 'Mobile Application Development 101', 27, 'friday', '13:00:00', '15:00:00'),
(5, 30, 17, 'ENG 111', 'CC103', 27, 'thursday', '17:00:00', '19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(11) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `sched_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `section_name`, `course_id`, `department_id`, `sched_id`) VALUES
(15, 'BSCS-2B', 18, 13, NULL),
(16, 'BSIT-2C', 19, 13, NULL),
(17, 'BSCE-1A', 20, 14, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_initial` char(1) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','staff','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `last_name`, `first_name`, `middle_initial`, `section_id`, `course_id`, `department_id`, `email`, `password`, `role`) VALUES
(26, 'Valledor', 'Alken', 'T', 15, 18, 13, 'kenitplaygaming@gmail.com', '$2y$10$teAY7Nbv3J6JcvC9SBhY1eI2mIW2vvc9.ajVW581sKTzXMe30w.eS', 'admin'),
(27, 'Tabotabo', 'Winston', 's', 16, 19, 13, 'johndoe@example.com', '$2y$10$kE4pzHMI3a7pOYvrpKZGOu9tOrvC3LxCbv1b/pusn9Rj1LqWZR.O.', 'staff'),
(28, 'Lakupin', 'kalvin', 'T', 16, 19, 13, 'muffinkenvalle@gmail.com', '$2y$10$firBAL.U5J6HIejwcabFoOf0OeK4lMQrjaTdLj9GC2BXjpZzKZeEi', 'student'),
(29, 'Zerna', 'WInston', 'f', 16, 19, 13, 'kenitplaygamings@gmail.comaa', '$2y$10$YdKeacWWSFxqYt8CXeAPyOQyGCAlExB8W4dYGrz97x1pjsmzM49sC', 'student'),
(30, 'Tabotabo', 's', 's', 17, 20, 14, 'kenitplaygaming@gmail.comsssssdasgsdgd', '$2y$10$G8l1E8arK2QglDM2uwKTg.NV/EGLQ.0WnPpCrFQxgFbgq.kw23lga', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_ibfk_1` (`department_id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_schedule_per_student` (`student_id`,`day`,`start_time`,`room`),
  ADD KEY `schedule_ibfk_2` (`section_id`);

--
-- Indexes for table `section`
--
ALTER TABLE `section`
  ADD PRIMARY KEY (`id`),
  ADD KEY `section_ibfk_1` (`course_id`),
  ADD KEY `section_ibfk_2` (`department_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_ibfk_1` (`section_id`),
  ADD KEY `user_ibfk_2` (`course_id`),
  ADD KEY `user_ibfk_3` (`department_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `section`
--
ALTER TABLE `section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `section`
--
ALTER TABLE `section`
  ADD CONSTRAINT `section_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `section_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `section` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `user_ibfk_3` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
