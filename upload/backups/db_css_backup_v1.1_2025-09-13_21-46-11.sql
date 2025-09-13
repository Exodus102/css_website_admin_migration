-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: db_css
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `credentials`
--

DROP TABLE IF EXISTS `credentials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `credentials` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
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
  `date_created` date NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credentials`
--

LOCK TABLES `credentials` WRITE;
/*!40000 ALTER TABLE `credentials` DISABLE KEYS */;
INSERT INTO `credentials` VALUES (1,'Jenrick','Panopio','Aran','09158100920','Binangonan','University MIS','University MIS','','aranjenrick@gmail.com','polskie123','Active','0000-00-00'),(6,'Jenrick','Dela Cruz','Aran','09208256071','Binangonan','Registrar','University MIS','','aaaaaa@gmail.com','bdsadasdsad','Active','2025-09-12'),(7,'Jenrick','Dela Cruz','Aran','09208256071','Morong','Registrar','CSS Coordinator','','ferf96989@gmail.com','polskie123','Active','2025-09-12');
/*!40000 ALTER TABLE `credentials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_backup`
--

DROP TABLE IF EXISTS `tbl_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_backup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `available_backups` varchar(100) NOT NULL,
  `version` int(20) NOT NULL,
  `size` varchar(100) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_backup`
--

LOCK TABLES `tbl_backup` WRITE;
/*!40000 ALTER TABLE `tbl_backup` DISABLE KEYS */;
INSERT INTO `tbl_backup` VALUES (7,'db_css_backup_v1.0_2025-09-13_21-46-05.sql',1,'0.01 MB','C:\\xampp\\htdocs\\css_website_admin_migration\\upload\\backups\\db_css_backup_v1.0_2025-09-13_21-46-05.sql','2025-09-13 19:46:05');
/*!40000 ALTER TABLE `tbl_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_campus`
--

DROP TABLE IF EXISTS `tbl_campus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_campus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campus_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_campus`
--

LOCK TABLES `tbl_campus` WRITE;
/*!40000 ALTER TABLE `tbl_campus` DISABLE KEYS */;
INSERT INTO `tbl_campus` VALUES (1,'Antipolo'),(2,'Angono'),(3,'Binangonan'),(4,'Cardona'),(5,'Cainta'),(6,'Morong'),(7,'Pililia'),(8,'Rodriguez'),(10,'Tanay'),(11,'Taytay');
/*!40000 ALTER TABLE `tbl_campus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_choices`
--

DROP TABLE IF EXISTS `tbl_choices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_choices` (
  `choices_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(50) NOT NULL,
  `choice_text` varchar(100) NOT NULL,
  PRIMARY KEY (`choices_id`),
  KEY `fk_question_id` (`question_id`),
  CONSTRAINT `fk_question_id` FOREIGN KEY (`question_id`) REFERENCES `tbl_questionaire` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_choices`
--

LOCK TABLES `tbl_choices` WRITE;
/*!40000 ALTER TABLE `tbl_choices` DISABLE KEYS */;
INSERT INTO `tbl_choices` VALUES (2,3,'asdasdasdasdas'),(195,67,'5'),(196,67,'4'),(197,67,'3'),(198,67,'2'),(199,67,'1'),(200,68,'5'),(201,68,'4'),(202,68,'3'),(203,68,'2'),(204,68,'1'),(205,69,'5'),(206,69,'4'),(207,69,'3'),(208,69,'2'),(209,69,'1'),(210,70,'5'),(211,70,'4'),(212,70,'3'),(213,70,'2'),(214,70,'1');
/*!40000 ALTER TABLE `tbl_choices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_customer_type`
--

DROP TABLE IF EXISTS `tbl_customer_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_customer_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_customer_type`
--

LOCK TABLES `tbl_customer_type` WRITE;
/*!40000 ALTER TABLE `tbl_customer_type` DISABLE KEYS */;
INSERT INTO `tbl_customer_type` VALUES (2,'Student'),(3,'Parent'),(4,'Faculty'),(5,'Alumni'),(6,'Staff'),(7,'Other');
/*!40000 ALTER TABLE `tbl_customer_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_division`
--

DROP TABLE IF EXISTS `tbl_division`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_division` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `division_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_division`
--

LOCK TABLES `tbl_division` WRITE;
/*!40000 ALTER TABLE `tbl_division` DISABLE KEYS */;
INSERT INTO `tbl_division` VALUES (1,'Office of The President'),(2,'Academic Affairs'),(3,'Administration and Finance Division'),(4,'Research, Development, Extension, and Production Development');
/*!40000 ALTER TABLE `tbl_division` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_questionaire`
--

DROP TABLE IF EXISTS `tbl_questionaire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_questionaire` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `question_survey` varchar(200) NOT NULL,
  `section` varchar(200) NOT NULL,
  `question` varchar(200) NOT NULL,
  `status` int(50) NOT NULL,
  `question_type` varchar(100) NOT NULL,
  `required` int(50) NOT NULL,
  `header` int(50) NOT NULL,
  `transaction_type` int(50) NOT NULL,
  `question_rendering` varchar(100) NOT NULL,
  PRIMARY KEY (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_questionaire`
--

LOCK TABLES `tbl_questionaire` WRITE;
/*!40000 ALTER TABLE `tbl_questionaire` DISABLE KEYS */;
INSERT INTO `tbl_questionaire` VALUES (3,'2025 Questionaire_v1.3','Section 2','asdasdas',0,'Dropdown',1,0,2,'None'),(64,'2025 Questionaire_v1.2','Section 2','Name (Optional)',1,'Text',0,0,2,'None'),(65,'2025 Questionaire_v1.2','Section 2','Contact No. (Optional)',1,'Text',0,0,2,'None'),(66,'2025 Questionaire_v1.2','Section 2','Click on the corresponding to your answer using the given scale below',1,'Description',1,0,2,'None'),(67,'2025 Questionaire_v1.2','Section 2','a. Knowledge of the Job',1,'Multiple Choice',1,0,0,'QoS'),(68,'2025 Questionaire_v1.2','Section 2','b. Accuracy in providing information',1,'Multiple Choice',1,0,0,'QoS'),(69,'2025 Questionaire_v1.2','Section 2','c. Delivery of prompt and appropriate service',1,'Multiple Choice',1,0,0,'QoS'),(70,'2025 Questionaire_v1.2','Section 2','d. Professionalism and skillfulness of the service personnel',1,'Multiple Choice',1,0,0,'QoS');
/*!40000 ALTER TABLE `tbl_questionaire` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_questionaireform`
--

DROP TABLE IF EXISTS `tbl_questionaireform`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_questionaireform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_survey` varchar(100) NOT NULL,
  `change_log` varchar(255) NOT NULL,
  `date_approved` date DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_questionaireform`
--

LOCK TABLES `tbl_questionaireform` WRITE;
/*!40000 ALTER TABLE `tbl_questionaireform` DISABLE KEYS */;
INSERT INTO `tbl_questionaireform` VALUES (1,'2025 Questionaire_v1.2','Updated survey questions and/or name.',NULL,'2025-09-09 17:26:34'),(2,'2025 Questionaire_v1.3','Initial survey creation.',NULL,'2025-09-08 15:34:56');
/*!40000 ALTER TABLE `tbl_questionaireform` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_responses`
--

DROP TABLE IF EXISTS `tbl_responses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_responses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question_id` int(11) NOT NULL,
  `response_id` int(11) DEFAULT NULL,
  `response` varchar(255) DEFAULT NULL,
  `comment` varchar(255) NOT NULL,
  `analysis` varchar(50) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `header` int(11) NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `question_rendering` varchar(255) DEFAULT NULL,
  `uploaded` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `question_id` (`question_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_responses`
--

LOCK TABLES `tbl_responses` WRITE;
/*!40000 ALTER TABLE `tbl_responses` DISABLE KEYS */;
/*!40000 ALTER TABLE `tbl_responses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_unit`
--

DROP TABLE IF EXISTS `tbl_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_unit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `campus_name` varchar(100) NOT NULL,
  `division_name` varchar(100) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_unit`
--

LOCK TABLES `tbl_unit` WRITE;
/*!40000 ALTER TABLE `tbl_unit` DISABLE KEYS */;
INSERT INTO `tbl_unit` VALUES (1,'Morong','Office of The President','Campus Management Information System'),(2,'Morong','Office of The President','Campus Planning, Monitoring and Evaluation');
/*!40000 ALTER TABLE `tbl_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tbl_unit_mis`
--

DROP TABLE IF EXISTS `tbl_unit_mis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tbl_unit_mis` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `division_name` varchar(100) NOT NULL,
  `unit_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tbl_unit_mis`
--

LOCK TABLES `tbl_unit_mis` WRITE;
/*!40000 ALTER TABLE `tbl_unit_mis` DISABLE KEYS */;
INSERT INTO `tbl_unit_mis` VALUES (1,'Office of The President','Campus Management Information System'),(2,'Office of The President','Campus Planning, Monitoring and Evaluation');
/*!40000 ALTER TABLE `tbl_unit_mis` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `two_factor_codes`
--

DROP TABLE IF EXISTS `two_factor_codes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `two_factor_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `two_factor_codes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `credentials` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `two_factor_codes`
--

LOCK TABLES `two_factor_codes` WRITE;
/*!40000 ALTER TABLE `two_factor_codes` DISABLE KEYS */;
/*!40000 ALTER TABLE `two_factor_codes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-14  3:46:12
