CREATE DATABASE  IF NOT EXISTS `wp_ayso1ref` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `wp_ayso1ref`;
-- MySQL dump 10.13  Distrib 5.7.20, for osx10.13 (x86_64)
--
-- Host: 127.0.0.1    Database: wp_ayso1ref
-- ------------------------------------------------------
-- Server version	5.7.20

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `crs_lastUpdate`
--

DROP TABLE IF EXISTS `crs_lastUpdate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_lastUpdate` (
  `timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_log`
--

DROP TABLE IF EXISTS `crs_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `projectKey` varchar(45) CHARACTER SET utf8 NOT NULL,
  `note` varchar(1024) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=418 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_report_notes`
--

DROP TABLE IF EXISTS `crs_report_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_report_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) DEFAULT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_reports`
--

DROP TABLE IF EXISTS `crs_reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `seq` int(11) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `text` varchar(255) NOT NULL,
  `notes` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_tmp_hrc`
--

DROP TABLE IF EXISTS `crs_tmp_hrc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_tmp_hrc` (
  `AYSOID` int(12) DEFAULT NULL,
  `Name` longtext,
  `First Name` text,
  `Last Name` text,
  `Address` text,
  `City` text,
  `State` text,
  `Zip` int(11) DEFAULT NULL,
  `Home Phone` text,
  `Cell Phone` text,
  `Email` text,
  `CertificationDesc` text,
  `CertDate` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `SAR` varchar(98) NOT NULL DEFAULT '',
  `Section` varchar(32) NOT NULL,
  `Area` varchar(32) NOT NULL,
  `Region` varchar(32) NOT NULL,
  `Membership Year` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_tmp_nocerts`
--

DROP TABLE IF EXISTS `crs_tmp_nocerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_tmp_nocerts` (
  `AYSOID` int(12) DEFAULT NULL,
  `Name` longtext,
  `First Name` text,
  `Last Name` text,
  `Address` text,
  `City` text,
  `State` text,
  `Zip` int(11) DEFAULT NULL,
  `Home Phone` text,
  `Cell Phone` text,
  `Email` text,
  `CertificationDesc` text,
  `CertDate` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `SAR` varchar(98) NOT NULL DEFAULT '',
  `Section` varchar(32) NOT NULL,
  `Area` varchar(32) NOT NULL,
  `Region` varchar(32) NOT NULL,
  `Membership Year` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_tmp_nra`
--

DROP TABLE IF EXISTS `crs_tmp_nra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_tmp_nra` (
  `Name` longtext,
  `City` text,
  `Email` text,
  `SAR` varchar(65) NOT NULL DEFAULT '',
  `Membership Year` varchar(12) DEFAULT NULL,
  `Last Name` text,
  `CertificationDesc` text,
  `State` text,
  `Zip` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_tmp_ra`
--

DROP TABLE IF EXISTS `crs_tmp_ra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_tmp_ra` (
  `AYSOID` int(12) DEFAULT NULL,
  `Name` longtext,
  `First Name` text,
  `Last Name` text,
  `Address` text,
  `City` text,
  `State` text,
  `Zip` int(11) DEFAULT NULL,
  `Home Phone` text,
  `Cell Phone` text,
  `Email` text,
  `CertificationDesc` text,
  `CertDate` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `SAR` varchar(98) NOT NULL DEFAULT '',
  `Section` varchar(32) NOT NULL,
  `Area` varchar(32) NOT NULL,
  `Region` varchar(32) NOT NULL,
  `Membership Year` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_tmp_ref_upgrades`
--

DROP TABLE IF EXISTS `crs_tmp_ref_upgrades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_tmp_ref_upgrades` (
  `Membership Year` varchar(12) DEFAULT NULL,
  `AYSOID` int(12) DEFAULT NULL,
  `Name` longtext,
  `First Name` text,
  `Last Name` text,
  `Address` text,
  `City` text,
  `State` text,
  `Zip` int(11) DEFAULT NULL,
  `Home Phone` text,
  `Cell Phone` text,
  `Email` text,
  `CertificationDesc` text,
  `CertDate` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `SAR` varchar(98) NOT NULL DEFAULT '',
  `Section` varchar(32) NOT NULL DEFAULT '',
  `Area` varchar(32) NOT NULL DEFAULT '',
  `Region` varchar(32) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_tmp_ri`
--

DROP TABLE IF EXISTS `crs_tmp_ri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_tmp_ri` (
  `AYSOID` int(12) DEFAULT NULL,
  `Name` longtext,
  `First Name` text,
  `Last Name` text,
  `Address` text,
  `City` text,
  `State` text,
  `Zip` int(11) DEFAULT NULL,
  `Home Phone` text,
  `Cell Phone` text,
  `Email` text,
  `CertificationDesc` text,
  `CertDate` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `SAR` varchar(98) NOT NULL DEFAULT '',
  `Section` varchar(32) NOT NULL,
  `Area` varchar(32) NOT NULL,
  `Region` varchar(32) NOT NULL,
  `Membership Year` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_tmp_rie`
--

DROP TABLE IF EXISTS `crs_tmp_rie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_tmp_rie` (
  `AYSOID` int(12) DEFAULT NULL,
  `Name` longtext,
  `First Name` text,
  `Last Name` text,
  `Address` text,
  `City` text,
  `State` text,
  `Zip` int(11) DEFAULT NULL,
  `Home Phone` text,
  `Cell Phone` text,
  `Email` text,
  `CertificationDesc` text,
  `CertDate` varchar(10) CHARACTER SET utf8 DEFAULT NULL,
  `SAR` varchar(98) NOT NULL DEFAULT '',
  `Section` varchar(32) NOT NULL,
  `Area` varchar(32) NOT NULL,
  `Region` varchar(32) NOT NULL,
  `Membership Year` varchar(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `crs_users`
--

DROP TABLE IF EXISTS `crs_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crs_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text,
  `enabled` tinyint(1) DEFAULT '1',
  `for_events` text,
  `hash` text,
  `admin` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'wp_ayso1ref'
--
/*!50003 DROP FUNCTION IF EXISTS `PROPER_CASE` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `PROPER_CASE`(`str` VARCHAR(255)) RETURNS varchar(255) CHARSET utf8
BEGIN 
  DECLARE c CHAR(1); 
  DECLARE s VARCHAR(255); 
  DECLARE i INT DEFAULT 1; 
  DECLARE bool INT DEFAULT 1; 
  DECLARE punct CHAR(18) DEFAULT ' ()[]{},.-_!@;:?/';
  SET s = LCASE( str ); 
  WHILE i <= LENGTH( str ) DO -- Jesse Palmer corrected from < to <= for last char 
    BEGIN 
      SET c = SUBSTRING( s, i, 1 ); 
      IF LOCATE( c, punct ) > 0 THEN 
        SET bool = 1; 
      ELSEIF bool=1 THEN  
        BEGIN 
          IF c >= 'a' AND c <= 'z' THEN  
            BEGIN 
              SET s = CONCAT(LEFT(s,i-1),UCASE(c),SUBSTRING(s,i+1)); 
              SET bool = 0; 
            END; 
          ELSEIF c >= '0' AND c <= '9' THEN 
            SET bool = 0; 
          END IF; 
        END; 
      END IF; 
      SET i = i+1; 
    END; 
  END WHILE; 
  RETURN s; 
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `SPLIT_STRING` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` FUNCTION `SPLIT_STRING`(str VARCHAR(255), delim VARCHAR(12), pos INT) RETURNS varchar(255) CHARSET utf8
BEGIN
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos),
       LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1),
       delim, '');
RETURN 1;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-11-20 17:59:13
