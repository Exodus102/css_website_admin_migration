-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2025 at 02:28 PM
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
  `middle_name` text NOT NULL,
  `last_name` text NOT NULL,
  `campus` text NOT NULL,
  `type` varchar(100) NOT NULL,
  `dp` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`user_id`, `first_name`, `middle_name`, `last_name`, `campus`, `type`, `dp`, `email`, `password`) VALUES
(1, 'Jenrick', 'Panopio', 'Aran', 'Binangonan', 'Campus Director', '', 'aranjenrick@gmail.com', 'b9fbm4ya');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_choices`
--

CREATE TABLE `tbl_choices` (
  `choices_id` int(11) NOT NULL,
  `question_id` int(50) NOT NULL,
  `choice_text` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_choices`
--

INSERT INTO `tbl_choices` (`choices_id`, `question_id`, `choice_text`) VALUES
(2, 3, 'asdasdasdasdas'),
(108, 38, 'Angono'),
(109, 38, 'Antipolo'),
(110, 38, 'Binangonan'),
(111, 38, 'Cainta'),
(112, 38, 'Cardona'),
(113, 38, 'Morong'),
(114, 38, 'Pililia'),
(115, 38, 'Rodriguez'),
(116, 38, 'Tanay'),
(117, 38, 'Taytay'),
(118, 39, 'Office of The President'),
(119, 39, 'Academic Affairs'),
(120, 39, 'Administration and Finance Division'),
(121, 39, 'Research, Development, Extension, and Production Division'),
(122, 40, 'Campus Management Information System'),
(123, 41, 'Student'),
(124, 41, 'Faculty'),
(125, 41, 'Alumni'),
(126, 41, 'Parent'),
(127, 41, 'Staff'),
(128, 41, 'Other'),
(129, 45, '5'),
(130, 45, '4'),
(131, 45, '3'),
(132, 45, '2'),
(133, 45, '1'),
(134, 46, '5'),
(135, 46, '4'),
(136, 46, '3'),
(137, 46, '2'),
(138, 46, '1'),
(139, 47, '5'),
(140, 47, '4'),
(141, 47, '3'),
(142, 47, '2'),
(143, 47, '1'),
(144, 48, '5'),
(145, 48, '4'),
(146, 48, '3'),
(147, 48, '2'),
(148, 48, '1');

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

--
-- Dumping data for table `tbl_questionaire`
--

INSERT INTO `tbl_questionaire` (`question_id`, `question_survey`, `section`, `question`, `status`, `question_type`, `required`, `header`, `transaction_type`, `question_rendering`) VALUES
(3, '2025 Questionaire_v1.3', 'Section 2', 'asdasdas', 0, 'Dropdown', 1, 0, 2, 'None'),
(38, '2025 Questionaire_v1.2', 'Section 2', 'Campus', 1, 'Dropdown', 1, 0, 2, 'None'),
(39, '2025 Questionaire_v1.2', 'Section 2', 'Division', 1, 'Dropdown', 1, 0, 2, 'None'),
(40, '2025 Questionaire_v1.2', 'Section 2', 'Unit', 1, 'Dropdown', 1, 0, 2, 'None'),
(41, '2025 Questionaire_v1.2', 'Section 2', 'Customer Type', 1, 'Multiple Choice', 1, 0, 2, 'None'),
(42, '2025 Questionaire_v1.2', 'Section 2', 'Name (Optional)', 1, 'Text', 0, 0, 2, 'None'),
(43, '2025 Questionaire_v1.2', 'Section 2', 'Contact No. (Optional)', 1, 'Text', 0, 0, 2, 'None'),
(44, '2025 Questionaire_v1.2', 'Section 2', 'Click on the corresponding to your answer using the given scale below', 1, 'Description', 1, 0, 2, 'None'),
(45, '2025 Questionaire_v1.2', 'Section 2', 'a. Knowledge of the Job', 1, 'Multiple Choice', 1, 0, 0, 'QoS'),
(46, '2025 Questionaire_v1.2', 'Section 2', 'b. Accuracy in providing information', 1, 'Multiple Choice', 1, 0, 0, 'QoS'),
(47, '2025 Questionaire_v1.2', 'Section 2', 'c. Delivery of prompt and appropriate service', 1, 'Multiple Choice', 1, 0, 0, 'QoS'),
(48, '2025 Questionaire_v1.2', 'Section 2', 'd. Professionalism and skillfulness of the service personnel', 1, 'Multiple Choice', 1, 0, 0, 'QoS');

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

--
-- Dumping data for table `tbl_questionaireform`
--

INSERT INTO `tbl_questionaireform` (`id`, `question_survey`, `change_log`, `date_approved`, `timestamp`) VALUES
(1, '2025 Questionaire_v1.2', 'Updated survey questions and/or name.', NULL, '2025-09-09 17:26:34'),
(2, '2025 Questionaire_v1.3', 'Initial survey creation.', NULL, '2025-09-08 15:34:56');

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
-- Indexes for table `tbl_choices`
--
ALTER TABLE `tbl_choices`
  ADD PRIMARY KEY (`choices_id`),
  ADD KEY `fk_question_id` (`question_id`);

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
-- Indexes for table `tbl_responses`
--
ALTER TABLE `tbl_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

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
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_choices`
--
ALTER TABLE `tbl_choices`
  MODIFY `choices_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `tbl_questionaire`
--
ALTER TABLE `tbl_questionaire`
  MODIFY `question_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `tbl_questionaireform`
--
ALTER TABLE `tbl_questionaireform`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_responses`
--
ALTER TABLE `tbl_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `two_factor_codes`
--
ALTER TABLE `two_factor_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
