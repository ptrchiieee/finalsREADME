-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 08, 2024 at 07:18 AM
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
-- Database: `student_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science', 'Study of computation and information processing.', '2024-12-06 08:24:57', '2024-12-06 08:24:57'),
(2, 'Information Technology', 'Focus on using technology to solve problems.', '2024-12-06 08:24:57', '2024-12-06 08:24:57'),
(3, 'Engineering', 'Design and build complex systems.', '2024-12-06 08:24:57', '2024-12-06 08:24:57');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `course_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `birthdate`, `course_id`, `created_at`, `updated_at`) VALUES
(26, 'John', 'Doe', 'john.doe@example.com', '2000-01-01', 1, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(27, 'Jane', 'Smith', 'jane.smith@example.com', '1999-02-15', 2, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(28, 'Alice', 'Johnson', 'alice.johnson@example.com', '1998-03-30', 3, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(29, 'Bob', 'Brown', 'bob.brown@example.com', '1997-07-10', 1, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(30, 'Charlie', 'Davis', 'charlie.davis@example.com', '1996-05-22', 2, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(31, 'David', 'Miller', 'david.miller@example.com', '1998-09-13', 3, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(32, 'Emma', 'Wilson', 'emma.wilson@example.com', '1999-12-01', 1, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(33, 'Fiona', 'Moore', 'fiona.moore@example.com', '2000-06-25', 2, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(34, 'George', 'Taylor', 'george.taylor@example.com', '2001-11-30', 3, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(35, 'Hannah', 'Anderson', 'hannah.anderson@example.com', '1997-08-17', 1, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(36, 'Isaac', 'Thomas', 'isaac.thomas@example.com', '1998-01-05', 2, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(37, 'Jack', 'Jackson', 'jack.jackson@example.com', '1996-03-11', 3, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(38, 'Karen', 'White', 'karen.white@example.com', '1999-10-29', 1, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(39, 'Liam', 'Harris', 'liam.harris@example.com', '2000-04-20', 2, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(40, 'Mia', 'Martin', 'mia.martin@example.com', '1997-02-08', 3, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(41, 'Noah', 'Thompson', 'noah.thompson@example.com', '2001-12-18', 1, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(42, 'Olivia', 'Garcia', 'olivia.garcia@example.com', '1998-07-22', 2, '2024-12-08 04:48:42', '2024-12-08 04:48:42'),
(43, 'parcel', 'shopee', 'shopee@example.com', '1999-07-07', 1, '2024-12-08 04:48:42', '2024-12-08 04:58:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `course_id` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
