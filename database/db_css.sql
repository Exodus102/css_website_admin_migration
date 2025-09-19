-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 19, 2025 at 11:25 AM
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
-- Database: `db_css`
--

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `user_id` int(11) NOT NULL,
  `first_name` text NOT NULL,
  `middle_name` text DEFAULT NULL,
  `last_name` text NOT NULL,
  `contact_number` varchar(100) NOT NULL,
  `campus` text NOT NULL,
  `unit` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `dp` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `status` text NOT NULL,
  `date_created` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`user_id`, `first_name`, `middle_name`, `last_name`, `contact_number`, `campus`, `unit`, `type`, `dp`, `email`, `password`, `status`, `date_created`) VALUES
(1, 'Jenrick', 'Panopio', 'Aran', '09158100920', 'Binangonan', 'University MIS', 'University MIS', '', 'aranjenrick@gmail.com', 'polskie123', 'Active', '0000-00-00'),
(6, 'Jenrick', 'Dela Cruz', 'Aran', '09208256071', 'Binangonan', 'Campus Directors', 'Campus Director', '', 'aaaaaa@gmail.com', 'bdsadasdsad', 'Active', '2025-09-12'),
(7, 'Jenrick', 'Dela Cruz', 'Aran', '09208256071', 'Binangonan', 'Registrar', 'CSS Coordinator', '', 'ferf96989@gmail.com', 'polskie123', 'Active', '2025-09-12'),
(8, 'Ramirr', 'Oppus', 'Villamarin', '09158100920', 'Binangonan', 'College of Computer Studies', 'CSS Coordinator', '', 'dlhor65@gmail.com', 'polskie123', 'Active', '2025-09-18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_backup`
--

CREATE TABLE `tbl_backup` (
  `id` int(11) NOT NULL,
  `available_backups` varchar(100) NOT NULL,
  `version` int(20) NOT NULL,
  `size` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_campus`
--

CREATE TABLE `tbl_campus` (
  `id` int(11) NOT NULL,
  `campus_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_campus`
--

INSERT INTO `tbl_campus` (`id`, `campus_name`) VALUES
(1, 'Antipolo'),
(2, 'Angono'),
(3, 'Binangonan'),
(4, 'Cardona'),
(5, 'Cainta'),
(6, 'Morong'),
(7, 'Pililia'),
(8, 'Rodriguez'),
(10, 'Tanay'),
(11, 'Taytay');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_choices`
--

CREATE TABLE `tbl_choices` (
  `choices_id` int(11) NOT NULL,
  `question_id` int(50) NOT NULL,
  `choice_text` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_type`
--

CREATE TABLE `tbl_customer_type` (
  `id` int(11) NOT NULL,
  `customer_type` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_type`
--

INSERT INTO `tbl_customer_type` (`id`, `customer_type`) VALUES
(2, 'Student'),
(3, 'Parent'),
(4, 'Faculty'),
(5, 'Alumni'),
(6, 'Staff'),
(7, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_division`
--

CREATE TABLE `tbl_division` (
  `id` int(11) NOT NULL,
  `division_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_division`
--

INSERT INTO `tbl_division` (`id`, `division_name`) VALUES
(1, 'Office of The President'),
(2, 'Academic Affairs'),
(3, 'Administration and Finance Division'),
(4, 'Research, Development, Extension, and Production Development'),
(6, 'Top Management');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_questionaire`
--

CREATE TABLE `tbl_questionaire` (
  `question_id` int(11) NOT NULL,
  `question_survey` varchar(200) NOT NULL,
  `section` varchar(200) NOT NULL,
  `question` varchar(200) NOT NULL,
  `status` int(50) NOT NULL,
  `question_type` varchar(100) NOT NULL,
  `required` int(50) NOT NULL,
  `header` int(50) NOT NULL,
  `transaction_type` int(50) NOT NULL,
  `question_rendering` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_questionaireform`
--

CREATE TABLE `tbl_questionaireform` (
  `id` int(11) NOT NULL,
  `question_survey` varchar(100) NOT NULL,
  `change_log` varchar(255) NOT NULL,
  `date_approved` date DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_report`
--

CREATE TABLE `tbl_report` (
  `id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_responses`
--

CREATE TABLE `tbl_responses` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `response_id` int(11) DEFAULT NULL,
  `response` varchar(255) DEFAULT NULL,
  `comment` varchar(255) NOT NULL,
  `analysis` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `header` int(11) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `question_rendering` varchar(255) DEFAULT NULL,
  `uploaded` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tally_report`
--

CREATE TABLE `tbl_tally_report` (
  `id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_unit`
--

CREATE TABLE `tbl_unit` (
  `id` int(11) NOT NULL,
  `campus_name` varchar(100) NOT NULL,
  `division_name` varchar(100) NOT NULL,
  `unit_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_unit`
--

INSERT INTO `tbl_unit` (`id`, `campus_name`, `division_name`, `unit_name`) VALUES
(1, 'Morong', 'Office of The President', 'Campus Management Information System'),
(2, 'Morong', 'Office of The President', 'Campus Planning, Monitoring and Evaluation'),
(3, 'Binangonan', 'Office of The President', 'Campus Planning, Monitoring and Evaluation'),
(4, 'Binangonan', 'Office of The President', 'Campus Management Information System'),
(5, 'Binangonan', 'Top Management', 'Campus Directors'),
(6, 'Binangonan', 'Academic Affairs', 'College of Accountancy'),
(7, 'Binangonan', 'Academic Affairs', 'College of Business'),
(8, 'Binangonan', 'Academic Affairs', 'College of Computer Studies'),
(9, 'Binangonan', 'Administration and Finance Division', 'Internal Audit Services'),
(10, 'Binangonan', 'Research, Development, Extension, and Production Development', 'Campus Research'),
(11, 'Binangonan', 'Academic Affairs', 'College of Social Work and Community Development'),
(12, 'Binangonan', 'Academic Affairs', 'Graduate School');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_unit_mis`
--

CREATE TABLE `tbl_unit_mis` (
  `id` int(11) NOT NULL,
  `division_name` varchar(100) NOT NULL,
  `unit_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_unit_mis`
--

INSERT INTO `tbl_unit_mis` (`id`, `division_name`, `unit_name`) VALUES
(1, 'Office of The President', 'Campus Management Information System'),
(2, 'Office of The President', 'Campus Planning, Monitoring and Evaluation'),
(4, 'Top Management', 'Office of the President'),
(5, 'Top Management', 'VP for Academic Affairs'),
(6, 'Top Management', 'VP for Admin and Finance'),
(7, 'Top Management', 'VP for RDEP'),
(8, 'Top Management', 'Campus Directors'),
(9, 'Office of The President', 'University Management Information System'),
(10, 'Office of The President', 'International Development and Special Programs'),
(11, 'Office of The President', 'Center for Life Long Learning'),
(12, 'Office of The President', 'Campus Sports Development'),
(13, 'Office of The President', 'Culture and Arts'),
(14, 'Office of The President', 'ISO Command Center'),
(15, 'Office of The President', 'Document Control Center'),
(16, 'Academic Affairs', 'College of Accountancy'),
(17, 'Academic Affairs', 'College of Business'),
(18, 'Academic Affairs', 'College of Computer Studies'),
(19, 'Academic Affairs', 'College of Social Work and Community Development'),
(20, 'Academic Affairs', 'Graduate School'),
(21, 'Academic Affairs', 'General Education Center'),
(22, 'Academic Affairs', 'Laboratory Schools'),
(23, 'Administration and Finance Division', 'Internal Audit Services'),
(24, 'Research, Development, Extension, and Production Development', 'Campus Research');

-- --------------------------------------------------------

--
-- Table structure for table `two_factor_codes`
--

CREATE TABLE `two_factor_codes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `tbl_backup`
--
ALTER TABLE `tbl_backup`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_campus`
--
ALTER TABLE `tbl_campus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_choices`
--
ALTER TABLE `tbl_choices`
  ADD PRIMARY KEY (`choices_id`),
  ADD KEY `fk_question_id` (`question_id`);

--
-- Indexes for table `tbl_customer_type`
--
ALTER TABLE `tbl_customer_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_division`
--
ALTER TABLE `tbl_division`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_questionaire`
--
ALTER TABLE `tbl_questionaire`
  ADD PRIMARY KEY (`question_id`);

--
-- Indexes for table `tbl_questionaireform`
--
ALTER TABLE `tbl_questionaireform`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_report`
--
ALTER TABLE `tbl_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_responses`
--
ALTER TABLE `tbl_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `tbl_tally_report`
--
ALTER TABLE `tbl_tally_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_unit`
--
ALTER TABLE `tbl_unit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_unit_mis`
--
ALTER TABLE `tbl_unit_mis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tbl_backup`
--
ALTER TABLE `tbl_backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_campus`
--
ALTER TABLE `tbl_campus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbl_choices`
--
ALTER TABLE `tbl_choices`
  MODIFY `choices_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_customer_type`
--
ALTER TABLE `tbl_customer_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_division`
--
ALTER TABLE `tbl_division`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_questionaire`
--
ALTER TABLE `tbl_questionaire`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_questionaireform`
--
ALTER TABLE `tbl_questionaireform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_report`
--
ALTER TABLE `tbl_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_responses`
--
ALTER TABLE `tbl_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_tally_report`
--
ALTER TABLE `tbl_tally_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_unit`
--
ALTER TABLE `tbl_unit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_unit_mis`
--
ALTER TABLE `tbl_unit_mis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_choices`
--
ALTER TABLE `tbl_choices`
  ADD CONSTRAINT `fk_question_id` FOREIGN KEY (`question_id`) REFERENCES `tbl_questionaire` (`question_id`);

--
-- Constraints for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  ADD CONSTRAINT `two_factor_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `credentials` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
