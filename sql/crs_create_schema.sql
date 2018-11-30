-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 30, 2018 at 04:22 PM
-- Server version: 5.7.24-0ubuntu0.18.04.1
-- PHP Version: 7.2.10-0ubuntu0.18.04.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ayso1ref_services`
--
CREATE DATABASE IF NOT EXISTS `ayso1ref_services` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `ayso1ref_services`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `BuildIRITable`$$
CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `BuildIRITable` ()  BEGIN
DROP TABLE IF EXISTS tmp_intermediate_referee_instructors;
CREATE TEMPORARY TABLE tmp_intermediate_referee_instructors (SELECT DISTINCT 
AYSOID, Name, SAR, CertificationDesc, CertDate FROM crs_rpt_ri WHERE (`CertificationDesc` = 'Referee Instructor' OR `CertificationDesc` = 'Referee Instructor') AND `CertDate` < '2018-09-01');   

DROP TABLE IF EXISTS crs_intermediate_referee_instructors;
CREATE TABLE crs_intermediate_referee_instructors SELECT 
	AYSOID, Name, SAR, CertificationDesc, CertDate 
	FROM 
		(SELECT 
			*,
			@rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
			@id:=`AYSOID`
		FROM 
			(SELECT * 
				FROM tmp_intermediate_referee_instructors
				GROUP BY `AYSOID`, FIELD(`CertificationDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Referee Instructor', 'Basic Referee Instructor', 'Grade2 Referee Instructor')
				) tmp 
			) ranked
	WHERE
		rank = 1;
ALTER TABLE `crs_intermediate_referee_instructors` ADD INDEX (`aysoid`);


SELECT *
FROM
    crs_intermediate_referee_instructors;
END$$

DROP PROCEDURE IF EXISTS `CertTweaks`$$
CREATE DEFINER=`root`@`%` PROCEDURE `CertTweaks` ()  BEGIN
# rick roberts
UPDATE `crs_certs` SET `Email` = 'ayso1sra@gmail.com' WHERE `AYSOID` = 97815888;
UPDATE `crs_certs` SET `SAR` = '1', `Area` = '', `Region` = '' WHERE `AYSOID` = 97815888;
UPDATE `crs_certs` SET `SAR` = '1', `Area` = '', `Region` = '' WHERE `SAR` = '1/';

# Update Referee Instructors
UPDATE `crs_certs` SET `CertificationDesc` = 'Regional Referee Instructor' WHERE `CertificationDesc` = 'Referee Instructor' OR `CertificationDesc` = 'Basic Referee Instructor';   
UPDATE `crs_certs` SET `CertificationDesc` = 'Intermediate Referee Instructor' WHERE `AYSOID` IN (SELECT AYSOID FROM crs_intermediate_referee_instructors); 

# Non-Board members on Section 1 portal
# Alfred Medina
UPDATE `crs_certs` SET `SAR` = '1', `Area` = 'H', `Region` = '' WHERE `AYSOID` = 51370299;

# Manuel Del Rio
UPDATE `crs_certs` SET `SAR` = '1', `Area` = 'H', `Region` = '' WHERE `AYSOID` = 55290662;

# Janet Orcutt
UPDATE `crs_certs` SET `SAR` = '1', `Area` = 'G', `Region` = '' WHERE `AYSOID` = 55189421;


# Merge records
# Chris Call
UPDATE `crs_certs` SET `AYSOID` = 66280719 WHERE `AYSOID` = 200284566;
UPDATE `crs_certs` SET `AYSOID` = 66280719 WHERE `AYSOID` = 202333632;

# Jon Swasey
UPDATE `crs_certs` SET `AYSOID` = 202650542 WHERE `AYSOID` = 70161548;

# Philip Maki
UPDATE `crs_certs` SET `AYSOID` = 65397057 WHERE `AYSOID` = 201245499;

# Michael Wolff
DELETE FROM `crs_certs` WHERE `AYSOID` = 56234203 AND `SAR` LIKE '1/D/%';

# Rick Ramirez
UPDATE `crs_certs` SET `AYSOID` = 200019230 WHERE `AYSOID` = 54288898;

# Michael Raycraft
DELETE FROM `crs_certs` WHERE `Email` = 'mlraycraft.aysoinstructor@gmail.com';
DELETE FROM `crs_certs` WHERE `Name` = 'Michael Raycraft' AND `CertificationDesc` LIKE 'National Referee Assessor';

# Peter Fink
DELETE FROM `crs_certs` WHERE `AYSOID` = 94012088;

# Vince O'Hara
DELETE FROM `crs_certs` WHERE `AYSOID` = 58214480;

# Eric Martinez
DELETE FROM `crs_certs` WHERE AYSOID = 99811587;

# Robert Osborne 
# duplicate eAYSO record
DELETE FROM `eAYSO.MY2016.certs` WHERE `AYSOID` = 79403530;
DELETE FROM `crs_certs` WHERE `AYSOID` = 79403530;

# missing Referee Instructor cert
INSERT INTO `crs_certs` 
(`Program Name`, `Volunteer Role`, AYSOID, Name, `First Name`, `Last Name`, Address, City, State, Zip, `Home Phone`, `Cell Phone`, Email, Gender, CertificationDesc, CertDate, SAR, Section, Area, Region, `Membership Year`)
VALUES ('MY2018', 'Volunteer', '71409033', 'Robert Osborne', 'Robert', 'Osborne', '5124 Inadale Ave', 'Los Angeles', 'CA', '90043', '(323) 293-7923', '(562) 216-4601', 'robertosborne72@gmail.com', 'M', 'Referee Instructor', '2018-10-04', '1/P/0076', '1', 'P', '76', 'MY2018');

# Jimmy Molinar
DELETE FROM `crs_certs` WHERE `AYSOID` = 56272832;

# Nelson Flores
UPDATE `crs_certs` SET `Membership Year` = 'MY2018' WHERE `AYSOID` = 94012355 AND `Membership Year` < 'MY2018';

# Dennis Raymond
UPDATE `crs_certs` SET `Membership Year` = 'MY2018' WHERE `AYSOID` = 55296033 AND `Membership Year` < 'MY2018';
 
# Invalid National Referee Assessors
# Yui-Bin	Chen
DELETE FROM `crs_certs` WHERE `AYSOID` = 57071121 AND `CertificationDesc` LIKE 'National Referee Assessor';
# Geoffrey	Falk
DELETE FROM `crs_certs` WHERE `AYSOID` = 59244326 AND `CertificationDesc` LIKE 'National Referee Assessor';
# Jody	Kinsey
DELETE FROM `crs_certs` WHERE `AYSOID` = 96383441 AND `CertificationDesc` LIKE 'National Referee Assessor';
# Bruce	Hancock
DELETE FROM `crs_certs` WHERE `AYSOID` = 99871834 AND `CertificationDesc` LIKE 'National Referee Assessor';
# Donald Ramsay
DELETE FROM `crs_certs` WHERE `AYSOID` = 204673909 AND `CertificationDesc` LIKE 'Referee Instructor Evaluator';

# Spencer Horwitz
DELETE FROM `crs_certs` WHERE `CertificationDesc`='National Referee Assessor' AND `AYSOID` = 95025758;

# Matt Kilroy
DELETE FROM `crs_certs` WHERE `Name` = 'Regional Commissioner';
END$$

DROP PROCEDURE IF EXISTS `compileVolIDs`$$
CREATE DEFINER=`root`@`%` PROCEDURE `compileVolIDs` ()  BEGIN
	DROP TABLE IF EXISTS `crs_vol_ids`;

	CREATE TABLE `crs_vol_ids` SELECT * FROM
		(SELECT DISTINCT
			`AYSO Volunteer ID` AS `AYSOID`
		FROM
			`crs_1_certs` 
		WHERE NOT `AYSO Volunteer ID` IS NULL    
			UNION SELECT DISTINCT
			`AYSOID`
		FROM
			`eAYSO.MY2017.certs` 
		WHERE length(`AYSOID`) > 3
			UNION SELECT DISTINCT
			`AYSOID`
		FROM
			`eAYSO.MY2016.certs`
		WHERE length(`AYSOID`) > 3
		) a
	ORDER BY `AYSOID`;

	SELECT * FROM `crs_vol_ids`;
END$$

DROP PROCEDURE IF EXISTS `distinctRegistrations`$$
CREATE DEFINER=`root`@`%` PROCEDURE `distinctRegistrations` ()  BEGIN
SELECT DISTINCT `AYSOID`, `Name`, `First Name`, `Last Name`, `Address`, `City`, `State`, `Zip`, `Home Phone`, `Cell Phone`, `Email`, `Gender`,`SAR` FROM crs_certs
UNION
SELECT DISTINCT `AYSOID`, `Name`, `FirstName` AS 'First Name', `LastName` AS 'Last Name', `Street` AS 'Address', `City`, `State`, `Zip`, `HomePhone`, `BusinessPhone` AS 'Cell Phone', `Email`, `Gender`, CONCAT(`SectionName`, '/', `AreaName`, '/', `RegionNumber`) AS 'SAR' FROM `eAYSO.MY2017.certs`
ORDER BY `SAR`, `Last Name`, `First Name`;
END$$

DROP PROCEDURE IF EXISTS `eAYSOHighestRefCert`$$
CREATE DEFINER=`root`@`%` PROCEDURE `eAYSOHighestRefCert` (`tableName` VARCHAR(128))  BEGIN

SET @id:= 0;
SET @dfields := "'National Referee', 'National 2 Referee', 'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 'U-8 Official & Safe Haven Referee', ''";
SET @fromTableName = CONCAT("`", tableName, "`");
SET @newTableName = CONCAT("`", tableName, '_highestRefCert`');

SET @s = CONCAT("DROP TABLE IF EXISTS ", @newTableName);

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

SET @s = CONCAT("
CREATE TABLE ", @newTableName, " SELECT * FROM
    (SELECT 
        `AYSOID`,
            `Name`,
            `FirstName` AS `First Name`,
            `LastName` AS `Last Name`,
            `Street` as `Address`,
            `City`,
            `State`,
            `Zip`,
            `HomePhone` AS `Home Phone`,
            `BusinessPhone` AS `Cell Phone`,
            `Email`,
            `Gender`,
            `CertificationDesc`,
            `CertDate`,
            `SectionAreaRegion` AS `SAR`,
            `SectionName` AS `Section`,
            `AreaName` AS `Area`,
            `RegionNumber` AS `Region`,
            `Membership Year`
    FROM
        (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        ", @fromTableName, "
    WHERE
        `CertificationDesc` LIKE '%Referee%'    
        AND NOT `CertificationDesc` LIKE '%Assessor%'
        AND NOT `CertificationDesc` LIKE '%Instructor%'
        AND NOT `CertificationDesc` LIKE '%Administrator%'
        AND NOT `CertificationDesc` LIKE '%VIP%'
        AND NOT `CertificationDesc` LIKE '%Course%'
        AND NOT `CertificationDesc` LIKE '%Scheduler%'
        AND NOT `CertificationDesc` = 'z-Online Regional Referee without Safe Haven' 
        AND NOT `CertificationDesc` = 'Z-Online Safe Haven Referee' 
        AND NOT `CertificationDesc` = 'Safe Haven Referee'
        AND NOT `CertificationDesc` = 'Z-Online Regional Referee'
    GROUP BY `AYSOID` , FIELD(`CertificationDesc`, 'National Referee', 'National 2 Referee', 'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 'U-8 Official & Safe Haven Referee', '')) ordered) ranked
    WHERE
        rank = 1
    ORDER BY FIELD(`CertificationDesc`, 'National Referee', 'National 2 Referee', 'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 'U-8 Official & Safe Haven Referee', '') , SAR, `Last Name` , `First Name` , AYSOID) hrc;");
    
PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;
    

END$$

DROP PROCEDURE IF EXISTS `prepEAYSOCSVTable`$$
CREATE DEFINER=`root`@`%` PROCEDURE `prepEAYSOCSVTable` (`certTable` VARCHAR(128))  BEGIN
SET @eaysoTable = CONCAT("`", certTable, "`");

SET @s = CONCAT("
	DROP TABLE IF EXISTS ", @eaysoTable, ";
");

PREPARE stmt FROM @s;  
EXECUTE stmt;  

DEALLOCATE PREPARE stmt;
    
SET @s = CONCAT("
	CREATE TABLE ", @eaysoTable," (
	  `AYSOID` text,
	  `Name` text,
	  `Street` text,
	  `City` text,
	  `State` text,
	  `Zip` text,
	  `HomePhone` text,
	  `BusinessPhone` text,
	  `Email` text,
	  `CertificationDesc` text,
	  `Gender` text,
	  `SectionAreaRegion` text,
	  `CertDate` text,
	  `ReCertDate` text,
	  `FirstName` text,
	  `LastName` text,
	  `SectionName` text,
	  `AreaName` text,
	  `RegionNumber` text,
	  `Membership Year` varchar(50)
	) ENGINE=InnoDB DEFAULT CHARSET=latin1;
");

PREPARE stmt FROM @s;  
EXECUTE stmt;  

DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `processBSCSV`$$
CREATE DEFINER=`root`@`%` PROCEDURE `processBSCSV` (`certCSV` VARCHAR(128))  BEGIN
SET @inTable = CONCAT("`", certCSV, "`");

SET @empty = '';
SET @space = ' ';
SET @delimTS = '/';
SET @delimDate = '/';

SET @s = CONCAT(' INSERT INTO crs_certs SELECT 
    `Program Name`,
    CONCAT("MY",`Program AYSO Membership Year`) AS `Membership Year`,
    `Volunteer Role`,
    `AYSO Volunteer ID` AS AYSOID,
	PROPER_CASE(CONCAT(`Volunteer First Name`, @space, `Volunteer Last Name`)) AS `Name`,
    PROPER_CASE(`Volunteer First Name`) AS `First Name`,
    PROPER_CASE(`Volunteer Last Name`) AS `Last Name`,
    PROPER_CASE(`Volunteer Address`) AS Address,
    PROPER_CASE(`Volunteer City`) AS City,
    `Volunteer State` AS State,
    `Volunteer Zip` AS Zip,
    `Volunteer Phone` AS `Home Phone`,
    `Volunteer Cell` AS `Cell Phone`,
    LCASE(`Volunteer Email`) AS Email,
    `Gender`,
    `AYSO Certifications` AS CertificationDesc,
    IF(`Date of Last AYSO Certification Update` = "" OR `Date of Last AYSO Certification Update` IS NULL, "", STR_TO_DATE(REPLACE(SPLIT_STRING(`Date of Last AYSO Certification Update`, " ", 1),"/", "."),GET_FORMAT(DATE,"USA"))) AS `CertDate`,
    IF(sar.`region` IS NULL
            OR sar.`region` = @empty,
        CONCAT(sar.`section`, @delimTS, sar.`area`),
        CONCAT(sar.`section`,
                @delimTS,
                sar.`area`,
                @delimTS,
                sar.`region`)) AS SAR,
    sar.`section` AS Section,
    sar.`area` AS Area,
    sar.`region` AS Region
FROM ',
    @inTable,
    ' csv
        INNER JOIN
    rs_sar sar ON csv.`Portal Name` = sar.`portalName`'
    );

PREPARE stmt FROM @s;  
EXECUTE stmt;  

DEALLOCATE PREPARE stmt;  

SET @s = CONCAT(' INSERT INTO crs_shcerts SELECT 
    `Program Name`,
    CONCAT("MY",`Program AYSO Membership Year`) AS `Membership Year`,
    `Volunteer Role`,
    `AYSO Volunteer ID` AS AYSOID,
	PROPER_CASE(CONCAT(`Volunteer First Name`, @space, `Volunteer Last Name`)) AS `Name`,
    PROPER_CASE(`Volunteer First Name`) AS `First Name`,
    PROPER_CASE(`Volunteer Last Name`) AS `Last Name`,
    PROPER_CASE(`Volunteer Address`) AS Address,
    PROPER_CASE(`Volunteer City`) AS City,
    `Volunteer State` AS State,
    `Volunteer Zip` AS Zip,
    `Volunteer Phone` AS `Home Phone`,
    `Volunteer Cell` AS `Cell Phone`,
    LCASE(`Volunteer Email`) AS Email,
    `Gender`,
    `AYSO Certifications` AS CertificationDesc,
    IF(`Date of Last AYSO Certification Update` = "" OR `Date of Last AYSO Certification Update` IS NULL, "", STR_TO_DATE(REPLACE(SPLIT_STRING(`Date of Last AYSO Certification Update`, " ", 1),"/", "."),GET_FORMAT(DATE,"USA"))) AS `CertDate`,
    IF(sar.`region` IS NULL
            OR sar.`region` = @empty,
        CONCAT(sar.`section`, @delimTS, sar.`area`),
        CONCAT(sar.`section`,
                @delimTS,
                sar.`area`,
                @delimTS,
                sar.`region`)) AS SAR,
    sar.`section` AS Section,
    sar.`area` AS Area,
    sar.`region` AS Region
FROM ',
    @inTable,
    ' csv
        INNER JOIN
    rs_sar sar ON csv.`Portal Name` = sar.`portalName`
WHERE `AYSO Certifications` LIKE \'%Safe Haven%\' AND NOT `AYSO Certifications` LIKE \'%without Safe Haven%\''
    );

PREPARE stmt FROM @s;  
EXECUTE stmt;  

DEALLOCATE PREPARE stmt;  
END$$

DROP PROCEDURE IF EXISTS `processEAYSOCSV`$$
CREATE DEFINER=`root`@`%` PROCEDURE `processEAYSOCSV` (`certTable` VARCHAR(128))  BEGIN
SET @inTable = CONCAT("`", certTable, "`");

SET @s = CONCAT('DELETE FROM ', @inTable, ' WHERE AYSOID = 0');
PREPARE stmt FROM @s;  
EXECUTE stmt;  

DEALLOCATE PREPARE stmt;

SET @s = CONCAT(' INSERT INTO crs_certs SELECT 
	`Membership Year` AS `Program Name`,
    `Membership Year`,
    "Volunteer" AS `Volunteer Role`,
    `AYSOID`,
	PROPER_CASE(`Name`) AS `Name`,
    PROPER_CASE(`FirstName`) AS `First Name`,
    PROPER_CASE(`LastName`) AS `Last Name`,
    PROPER_CASE(`Street`) AS Address,
    PROPER_CASE(`City`) AS `City`,
    `State`,
    REPLACE(`Zip`,"\'", "") AS `Zip`,
    `HomePhone` AS `Home Phone`,
    `BusinessPhone` AS `Cell Phone`,
    LCASE(`Email`) AS Email,
    `Gender`,
    `CertificationDesc`,
    IF(`CertDate` = "" OR `CertDate` IS NULL, "", STR_TO_DATE(REPLACE(SPLIT_STRING(`CertDate`, " ", 1),"/", "."),GET_FORMAT(DATE,"USA"))) AS `CertDate`,
    `SectionAreaRegion` AS SAR,
    `SectionName` AS Section,
    `AreaName` AS Area,
    `RegionNumber` AS Region
FROM ',
    @inTable);

PREPARE stmt FROM @s;  
EXECUTE stmt;  

DEALLOCATE PREPARE stmt;

SET @s = CONCAT(' INSERT INTO crs_shcerts SELECT 
	`Membership Year` AS `Program Name`,
    `Membership Year`,
    "Volunteer" AS `Volunteer Role`,
    `AYSOID`,
	PROPER_CASE(`Name`) AS `Name`,
    PROPER_CASE(`FirstName`) AS `First Name`,
    PROPER_CASE(`LastName`) AS `Last Name`,
    PROPER_CASE(`Street`) AS Address,
    PROPER_CASE(`City`) AS `City`,
    `State`,
    REPLACE(`Zip`,"\'", "") AS `Zip`,
    `HomePhone` AS `Home Phone`,
    `BusinessPhone` AS `Cell Phone`,
    LCASE(`Email`) AS Email,
    `Gender`,
    `CertificationDesc`,
    IF(`CertDate` = "" OR `CertDate` IS NULL, "", STR_TO_DATE(REPLACE(SPLIT_STRING(`CertDate`, " ", 1),"/", "."),GET_FORMAT(DATE,"USA"))) AS `CertDate`,
    `SectionAreaRegion` AS SAR,
    `SectionName` AS Section,
    `AreaName` AS Area,
    `RegionNumber` AS Region
FROM ',
    @inTable,
'WHERE `CertificationDesc` LIKE "%Safe Haven%" AND NOT `CertificationDesc` LIKE "%without Safe Haven%"
');

PREPARE stmt FROM @s;  
EXECUTE stmt;  

DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `RefreshCertDateErrors`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshCertDateErrors` ()  BEGIN
DROP TABLE IF EXISTS `crs_tmp_cert_date_errors`;
SET @s = CONCAT("CREATE TABLE `crs_tmp_cert_date_errors`
SELECT 
    *
FROM
    (SELECT DISTINCT
        c.`Membership Year`,
		c.`AYSOID`,
		c.`Name`,
		c.`CertificationDesc` AS `Course Desc`,
		c.`CertDate` AS `Course Date`,
		u.`CertificationDesc` AS `Upgrade Desc`,
		u.`CertDate` AS `Upgrade Date`,
		c.`Section`,
		c.`Area`,
		c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'National Referee Course'
            AND u.`CertificationDesc` LIKE 'National Referee' UNION SELECT DISTINCT
        c.`Membership Year`,
		c.`AYSOID`,
		c.`Name`,
		c.`CertificationDesc` AS `Course Desc`,
		c.`CertDate` AS `Course Date`,
		u.`CertificationDesc` AS `Upgrade Date`,
		u.`CertDate` AS `Upgrade Desc`,
		c.`Section`,
		c.`Area`,
		c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'Advanced Referee Course'
            AND u.`CertificationDesc` LIKE 'Avanced Referee' UNION SELECT DISTINCT
        c.`Membership Year`,
            c.`AYSOID`,
            c.`Name`,
            c.`CertificationDesc` AS `Course Desc`,
            c.`CertDate` AS `Course Date`,
            u.`CertificationDesc` AS `Upgrade Date`,
            u.`CertDate` AS `Upgrade Desc`,
            c.`Section`,
            c.`Area`,
            c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'Intermediate Referee Course'
            AND u.`CertificationDesc` LIKE 'Intermediate Referee' UNION SELECT DISTINCT
        c.`Membership Year`,
		c.`AYSOID`,
		c.`Name`,
		c.`CertificationDesc` AS `Course Desc`,
		c.`CertDate` AS `Course Date`,
		u.`CertificationDesc` AS `Upgrade Date`,
		u.`CertDate` AS `Upgrade Desc`,
		c.`Section`,
		c.`Area`,
		c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'National Referee Assessor Course'
            AND u.`CertificationDesc` LIKE 'National Referee Assessor' UNION SELECT DISTINCT
        c.`Membership Year`,
		c.`AYSOID`,
		c.`Name`,
		c.`CertificationDesc` AS `Course Desc`,
		c.`CertDate` AS `Course Date`,
		u.`CertificationDesc` AS `Upgrade Desc`,
		u.`CertDate` AS `Upgrade Desc`,
		c.`Section`,
		c.`Area`,
		c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'Referee Assessor Course'
            AND u.`CertificationDesc` LIKE 'Referee Assessor' UNION SELECT DISTINCT
        c.`Membership Year`,
            c.`AYSOID`,
            c.`Name`,
            c.`CertificationDesc` AS `Course Desc`,
            c.`CertDate` AS `Course Date`,
            u.`CertificationDesc` AS `Upgrade Date`,
            u.`CertDate` AS `Upgrade Desc`,
            c.`Section`,
            c.`Area`,
            c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'Advanced Referee Instructor Course'
            AND u.`CertificationDesc` LIKE 'Advanced Referee Instructor' UNION SELECT DISTINCT
        c.`Membership Year`,
		c.`AYSOID`,
		c.`Name`,
		c.`CertificationDesc` AS `Course Desc`,
		c.`CertDate` AS `Course Date`,
		u.`CertificationDesc` AS `Upgrade Date`,
		u.`CertDate` AS `Upgrade Desc`,
		c.`Section`,
		c.`Area`,
		c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'Referee Instructor Course'
            AND u.`CertificationDesc` LIKE 'Referee Instructor' UNION SELECT DISTINCT
        c.`Membership Year`,
		c.`AYSOID`,
		c.`Name`,
		c.`CertificationDesc` AS `Course Desc`,
		c.`CertDate` AS `Course Date`,
		u.`CertificationDesc` AS `Upgrade Date`,
		u.`CertDate` AS `Upgrade Desc`,
		c.`Section`,
		c.`Area`,
		c.`Region`
    FROM
        crs_certs c
    INNER JOIN crs_certs u ON c.AYSOID = u.AYSOID
        AND ABS(DATEDIFF(c.`CertDate`, u.`CertDate`)) < 7
    WHERE
        c.`CertificationDesc` LIKE 'Referee Instructor Evaluator Course'
            AND u.`CertificationDesc` LIKE 'Referee Instructor Evaluator') a
GROUP BY `Name`
ORDER BY FIELD(`Upgrade Desc`,
        'National Referee',
        'Advanced Referee',
        'Intermediate Referee',
        'National Referee Assessor',
        'Referee Assessor',
        'Referee Instructor',
        'Advanced Referee Instructor',
        'Referee Instructor Evaluator') , `Section` , `Area` , `Region`"
);

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

END$$

DROP PROCEDURE IF EXISTS `RefreshCompositeRefCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshCompositeRefCerts` ()  BEGIN
DROP TABLE IF EXISTS crs_rpt_ref_certs;

CREATE TABLE crs_rpt_ref_certs SELECT * FROM
    (SELECT DISTINCT
        hrc.*,
		sh.CertificationDesc AS shCertificationDesc,
		sh.CertDate AS shCertDate,
		cdc.CDCCert AS cdcCertficationDesc,
		cdc.CDCCertDate AS cdcCertDate 
    FROM
        crs_rpt_hrc hrc
			LEFT JOIN 
        crs_rpt_safehaven sh ON hrc.aysoid = sh.aysoid
			LEFT JOIN
		crs_rpt_ref_cdc cdc ON hrc.AYSOID = cdc.aysoid) a
ORDER BY SAR;

END$$

DROP PROCEDURE IF EXISTS `RefreshConcussionCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshConcussionCerts` ()  BEGIN
SET @id:= 0;

DROP TABLE IF EXISTS crs_cdc;
SET @s = CONCAT("
CREATE TABLE crs_cdc SELECT 
	`Program Name`,
	`Membership Year`,
	`Volunteer Role`,
	`AYSOID`,
	`Name`,
	`First Name`,
	`Last Name`,
	`Address`,
	`City`,
	`State`,
	`Zip`,
	`Home Phone`,
	`Cell Phone`,
	`Email`,
    `Gender`,
	`CertificationDesc`,
	`CertDate`,
	`SAR`,
	`Section`,
	`Area`,
	`Region`
FROM
    (SELECT 
        *
    FROM
        (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM (SELECT * FROM 
        crs_certs
    WHERE
        `CertificationDesc` LIKE '%Concussion%' 
    GROUP BY `AYSOID`, `CertDate` DESC, `Membership Year` DESC) con ) ordered) ranked
WHERE
    rank = 1
ORDER BY `Section` , `Area` , `Region` , `Last Name`;
");
    
PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

ALTER TABLE crs_cdc ADD INDEX (`AYSOID`);

END$$

DROP PROCEDURE IF EXISTS `RefreshDupicateRefCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshDupicateRefCerts` ()  BEGIN


DROP TABLE IF EXISTS tmp_duprefcerts;

CREATE TEMPORARY TABLE tmp_duprefcerts SELECT 
    *
FROM
    (SELECT 
        e.`AYSOID`,
        bs.`AYSOID` AS `bsAYSOID`,
		bs.`Name`,
		bs.`First Name`,
		bs.`Last Name`,
		bs.`Address`,
		bs.`City`,
		bs.`Email`,
        bs.`Gender`,
        bs.`CertDate`

    FROM
        crs_rpt_hrc e
    LEFT JOIN crs_rpt_hrc bs USING (`Email`, `Gender`, `CertDate`)
 ) g
WHERE
    `AYSOID` - `bsAYSOID` < 0
        AND `AYSOID` <= 99999999
        AND `bsAYSOID` > 99999999;
END$$

DROP PROCEDURE IF EXISTS `RefreshHighestCertification`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshHighestCertification` ()  BEGIN
SET @@GLOBAL.sql_mode=(SELECT REPLACE(@@GLOBAL.sql_mode,'ONLY_FULL_GROUP_BY',''));

SET @id:= 0;

DROP TABLE IF EXISTS crs_rpt_hrc;

SET @s = CONCAT("
	CREATE TABLE crs_rpt_hrc SELECT DISTINCT
    *
FROM
    (SELECT 
        `AYSOID`,
            `Name`,
            `First Name`,
            `Last Name`,
            `Address`,
            `City`,
            `State`,
            `Zip`,
            `Home Phone`,
            `Cell Phone`,
            `Email`,
            `Gender`,
            `CertificationDesc`,
            `CertDate`,
            `SAR`,
            `Section`,
            `Area`,
            `Region`,
            `Membership Year`
    FROM
        (SELECT 
        *,
            @rankID:=IF(@id = `AYSOID` AND @sar = `SAR`, @rankID + 1, 1) AS rankID,
            @id:=`AYSOID`,
            @sar:=`SAR`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        NOT `CertificationDesc` LIKE '%Assessor%'
            AND NOT `CertificationDesc` LIKE '%Instructor%'
            AND NOT `CertificationDesc` LIKE '%Administrator%'
            AND NOT `CertificationDesc` LIKE '%VIP%'
            AND NOT `CertificationDesc` LIKE '%Course%'
            AND NOT `CertificationDesc` LIKE '%Scheduler%'
            AND `CertificationDesc` <> 'z-Online Regional Referee without Safe Haven'
            AND `CertificationDesc` <> 'Z-Online Regional Referee'
            AND `CertificationDesc` <> 'Z-Online Safe Haven Referee'
            AND `CertificationDesc` <> 'Safe Haven Referee'
    GROUP BY `AYSOID` , `Section`, `Area` , FIELD(`CertificationDesc`, 'National Referee', 'National 2 Referee', 'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 'U-8 Official & Safe Haven Referee', '')) ordered) ranked
    WHERE
        rankID = 1
    ORDER BY FIELD(`CertificationDesc`, 'National Referee', 'National 2 Referee', 'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 'U-8 Official & Safe Haven Referee', ''), `Section`, `Area`, `Region`, `Last Name`, `First Name`, AYSOID) hrc;
");

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

ALTER TABLE `crs_rpt_hrc` ADD INDEX (`AYSOID`);

END$$

DROP PROCEDURE IF EXISTS `RefreshIntermediateRefereeInstructors`$$
CREATE DEFINER=`root`@`127.0.0.1` PROCEDURE `RefreshIntermediateRefereeInstructors` ()  BEGIN
UPDATE tmp_rpt_ri SET `CertificationDesc` = 'Regional Referee Instructor' WHERE `CertificationDesc` = 'Referee Instructor' OR `CertificationDesc` = 'Basic Referee Instructor';   

# Voluteer updates
UPDATE tmp_rpt_ri SET `CertificationDesc` = 'Intermediate Referee Instructor' WHERE `AYSOID` IN (SELECT AYSOID FROM crs_intermediate_referee_instructors); 
END$$

DROP PROCEDURE IF EXISTS `RefreshNationalRefereeAssessors`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshNationalRefereeAssessors` ()  BEGIN
SET @id:= 0;

DROP TABLE IF EXISTS tmp_nra;

CREATE TEMPORARY TABLE tmp_nra SELECT * FROM
    (SELECT 
        `AYSOID`,
		`Name`,
		`First Name`,
		`Last Name`,
		`Address`,
		`City`,
		`State`,
		`Zip`,
		`Home Phone`,
		`Cell Phone`,
		`Email`,
		`CertificationDesc`,
		`CertDate`,
		`SAR`,
		`Section`,
		`Area`,
        `Region`,
		`Membership Year`
    FROM
        (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        `CertificationDesc` LIKE 'National Referee Assessor' AND
        (`Membership Year` = 'MY2018' OR `Membership Year` = 'MY2017')
    GROUP BY `AYSOID` ) ordered) ranked
    WHERE
        rank = 1
	GROUP BY `Email`
    ORDER BY CertificationDesc , `Section` , `Area` , `Last Name` , `First Name` , `AYSOID`) ra;
    
    UPDATE tmp_nra SET `SAR`= '1/', `Area`= '' WHERE `AYSOID` = 97815888;
    
    DROP TABLE IF EXISTS crs_rpt_nra;

	CREATE TABLE crs_rpt_nra SELECT * FROM tmp_nra
    ORDER BY CertificationDesc , `Section` , `Area` , `Last Name` , `First Name` , `AYSOID`;
    
END$$

DROP PROCEDURE IF EXISTS `RefreshRefCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshRefCerts` ()  BEGIN
DROP TABLE IF EXISTS crs_refcerts;

CREATE TABLE crs_refcerts SELECT * FROM
    crs_certs
WHERE
    (`CertificationDesc` LIKE '%Referee%'
    OR `CertificationDesc` LIKE '%Official%')
        AND `CertificationDesc` <> 'Z-Online Regional Referee%'
        AND `CertificationDesc` <> 'Regional Referee online%'
        AND `Volunteer Role` <> 'General Volunteer (Not Coach, Referee or Manager)'
		AND `Volunteer Role` <> 'TEST ONLY - Referee';
        
UPDATE `crs_refcerts` 
SET 
    `CertificationDesc` = 'Regional Referee'
WHERE
    `CertificationDesc` = 'Regional Referee & Safe Haven Referee';

UPDATE `crs_refcerts` 
SET 
    `CertificationDesc` = 'Assistant Referee'
WHERE
    `CertificationDesc` = 'Assistant Referee & Safe Haven Referee';

UPDATE `crs_refcerts` 
SET 
    `CertificationDesc` = 'U-8 Official'
WHERE
    `CertificationDesc` = 'U-8 Official & Safe Haven Referee';
END$$

DROP PROCEDURE IF EXISTS `RefreshRefConcussionCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshRefConcussionCerts` ()  BEGIN
SET @id:= 0;

DROP TABLE IF EXISTS crs_rpt_ref_cdc;
SET @s = CONCAT("CREATE TABLE crs_rpt_ref_cdc SELECT 
hrc.*, cdc.CertificationDesc AS CDCCert, cdc.CertDate AS CDCCertDate 
FROM `crs_cdc` cdc
RIGHT JOIN `crs_rpt_hrc` hrc 
ON cdc.AYSOID = hrc.AYSOID
ORDER BY `Section`, `Area`, `Region`;
");
    
PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

ALTER TABLE crs_rpt_ref_cdc ADD INDEX (`AYSOID`);

END$$

DROP PROCEDURE IF EXISTS `RefreshRefereeAssessors`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshRefereeAssessors` ()  BEGIN
SET @id:= 0;

DROP TABLE IF EXISTS crs_rpt_ra;

CREATE TABLE crs_rpt_ra SELECT * FROM
    (SELECT 
        `AYSOID`,
		`Name`,
		`First Name`,
		`Last Name`,
		`Address`,
		`City`,
		`State`,
		`Zip`,
		`Home Phone`,
		`Cell Phone`,
		`Email`,
		`CertificationDesc`,
		`CertDate`,
		`SAR`,
		`Section`,
		`Area`,
		`Region`,
		`Membership Year`
    FROM
        (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        `CertificationDesc` LIKE '%Assessor%'
            AND NOT `CertificationDesc` LIKE '%Instructor%'
            AND NOT `CertificationDesc` LIKE '%Administrator%'
            AND NOT `CertificationDesc` LIKE '%VIP%'
            AND NOT `CertificationDesc` LIKE '%Course%'
            AND NOT `CertificationDesc` LIKE 'Regional Referee'
            AND NOT `CertificationDesc` LIKE 'z-Online'
            AND NOT `CertificationDesc` LIKE '%Safe Haven%'
            AND NOT `CertificationDesc` LIKE 'Assistant%'
            AND NOT `CertificationDesc` LIKE '%Official%'
    GROUP BY `AYSOID` , FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor')) ordered) ranked
    WHERE
        rank = 1
    GROUP BY `Email`
    ORDER BY CertificationDesc , `Section` , `Area` , `Region` , `Last Name` , `First Name` , AYSOID) ra;
END$$

DROP PROCEDURE IF EXISTS `RefreshRefereeInstructorEvaluators`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshRefereeInstructorEvaluators` ()  BEGIN
SET @id:= 0;

DROP TABLE IF EXISTS crs_rpt_rie;

CREATE TEMPORARY TABLE crs_rpt_rie SELECT DISTINCT * FROM
    (SELECT 
        `AYSOID`,
		`Name`,
		`First Name`,
		`Last Name`,
		`Address`,
		`City`,
		`State`,
		`Zip`,
		`Home Phone`,
		`Cell Phone`,
		`Email`,
		`CertificationDesc` AS 'InstructorEvaluatorCert',
		`CertDate` AS 'InstructorEvaluatorCertDate',
		`ARCert` AS 'RefereeInstructorCert',
        `ARCertDate` AS 'RefereeInstructorCertDate',
		`SAR`,
		`Section`,
		`Area`,
		`Region`,
		`Membership Year`
    FROM
        (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        rc.*,
		ar.`CertificationDesc` AS 'ARCert',
        ar.`CertDate` AS 'ARCertDate'
    FROM
        `crs_refcerts` rc INNER JOIN `crs_refcerts` ar ON rc.AYSOID = ar.AYSOID
    WHERE
        rc.`CertificationDesc` = 'Referee Instructor Evaluator'
            AND (rc.`Membership Year` = 'MY2018' OR rc.`Membership Year` = 'MY2017')
            AND ar.`CertificationDesc` LIKE '%Referee Instructor'
    ORDER BY rc.`CertDate` DESC) ordered) ranked
    WHERE
        rank = 1
    ORDER BY `Section`, `Area`, `Region`, `Last Name`, `First Name`) rie;

END$$

DROP PROCEDURE IF EXISTS `RefreshRefereeInstructors`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshRefereeInstructors` ()  BEGIN
SET @id:= 0;

DROP TABLE IF EXISTS crs_rpt_ri;

CREATE TEMPORARY TABLE crs_rpt_ri SELECT * FROM
    (SELECT 
        `AYSOID`,
		`Name`,
		`First Name`,
		`Last Name`,
		`Address`,
		`City`,
		`State`,
		`Zip`,
		`Home Phone`,
		`Cell Phone`,
		`Email`,
		`CertificationDesc`,
		`CertDate`,
		`SAR`,
		`Section`,
		`Area`,
		`Region`,
		`Membership Year`
    FROM
        (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        `CertificationDesc` LIKE '%Instructor%'
            AND NOT `CertificationDesc` LIKE '%Evaluator%'
            AND NOT `CertificationDesc` LIKE '%Assessor%'
            AND NOT `CertificationDesc` LIKE '%Course%'
            AND NOT `CertificationDesc` LIKE '%Regional%'
            AND NOT `CertificationDesc` LIKE '%Assistant%'
            AND NOT `CertificationDesc` LIKE '%Official%'
            AND NOT `CertificationDesc` LIKE '%Webinar%'
            AND NOT `CertificationDesc` LIKE '%Online%'
            AND NOT `CertificationDesc` LIKE '%Safe Haven%'
    GROUP BY `AYSOID` , FIELD(`CertificationDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Referee Instructor', 'Basic Referee Instructor', 'Grade2 Referee Instructor')) ordered) ranked
    WHERE
        rank = 1) ri;
    
END$$

DROP PROCEDURE IF EXISTS `RefreshRefereeUpgradeCandidates`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshRefereeUpgradeCandidates` ()  BEGIN
DROP TABLE IF EXISTS tmp_NatRC;

CREATE TEMPORARY TABLE tmp_NatRC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        LOWER(`CertificationDesc`) = LOWER('National Referee Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_NatR;

CREATE TEMPORARY TABLE tmp_NatR SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    LOWER(`CertificationDesc`) = LOWER('National Referee');

DROP TABLE IF EXISTS tmp_ref_upgrades;

CREATE TEMPORARY TABLE tmp_ref_upgrades SELECT DISTINCT course.`AYSOID`,
    course.`Name`,
    course.`First Name`,
    course.`Last Name`,
    course.`Address`,
    course.`City`,
    course.`State`,
    course.`Zip`,
    course.`Home Phone`,
    course.`Cell Phone`,
    course.`Email`,
    course.`Gender`,
    course.`CertificationDesc`,
    course.`CertDate`,
    course.`SAR`,
    course.`Section`,
    course.`Area`,
    course.`Region`,
    course.`Membership Year` FROM
    tmp_NatRC course
        LEFT JOIN
    tmp_NatR upgraded ON course.`AYSOID` = upgraded.`AYSOID`
WHERE
    upgraded.`CertDate` IS NULL;
        

DROP TABLE IF EXISTS tmp_AdvRC;

CREATE TEMPORARY TABLE tmp_AdvRC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        LOWER(`CertificationDesc`) = LOWER('Advanced Referee Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_AdvR;

CREATE TEMPORARY TABLE tmp_AdvR SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    LOWER(`CertificationDesc`) = LOWER('Advanced Referee');

INSERT INTO tmp_ref_upgrades SELECT DISTINCT
		course.`AYSOID`,
		course.`Name`,
		course.`First Name`,
		course.`Last Name`,
		course.`Address`,
		course.`City`,
		course.`State`,
		course.`Zip`,
		course.`Home Phone`,
		course.`Cell Phone`,
		course.`Email`,
		course.`Gender`,
		course.`CertificationDesc`,
		course.`CertDate`,
		course.`SAR`,
		course.`Section`,
		course.`Area`,
		course.`Region`, 
		course.`Membership Year`
    FROM
        tmp_AdvRC course LEFT JOIN tmp_AdvR upgraded ON course.`AYSOID` = upgraded.`AYSOID`
    WHERE
        upgraded.`CertDate` IS NULL;
        

DROP TABLE IF EXISTS tmp_IntRC;

CREATE TEMPORARY TABLE tmp_IntRC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        LOWER(`CertificationDesc`) = LOWER('Intermediate Referee Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_IntR;

CREATE TEMPORARY TABLE tmp_IntR SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    LOWER(`CertificationDesc`) = LOWER('Intermediate Referee');

INSERT INTO tmp_ref_upgrades SELECT DISTINCT
		course.`AYSOID`,
		course.`Name`,
		course.`First Name`,
		course.`Last Name`,
		course.`Address`,
		course.`City`,
		course.`State`,
		course.`Zip`,
		course.`Home Phone`,
		course.`Cell Phone`,
		course.`Email`,
		course.`Gender`,
		course.`CertificationDesc`,
		course.`CertDate`,
		course.`SAR`,
		course.`Section`,
		course.`Area`,
		course.`Region`, 
		course.`Membership Year`
    FROM
        tmp_IntRC course LEFT JOIN tmp_IntR upgraded ON course.`AYSOID` = upgraded.`AYSOID`
    WHERE
        upgraded.`CertDate` IS NULL;
        
DROP TABLE IF EXISTS tmp_IntR;
DROP TABLE IF EXISTS tmp_IntRC;     


DROP TABLE IF EXISTS tmp_RAC;

CREATE TEMPORARY TABLE tmp_RAC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        LOWER(`CertificationDesc`) = LOWER('Referee Assessor Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_RA;

CREATE TEMPORARY TABLE tmp_RA SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    LOWER(`CertificationDesc`) = LOWER('Referee Assessor');

INSERT INTO tmp_ref_upgrades SELECT DISTINCT
		course.`AYSOID`,
		course.`Name`,
		course.`First Name`,
		course.`Last Name`,
		course.`Address`,
		course.`City`,
		course.`State`,
		course.`Zip`,
		course.`Home Phone`,
		course.`Cell Phone`,
		course.`Email`,
		course.`Gender`,
		course.`CertificationDesc`,
		course.`CertDate`,
		course.`SAR`,
		course.`Section`,
		course.`Area`,
		course.`Region`, 
		course.`Membership Year`
    FROM
        tmp_RAC course LEFT JOIN tmp_RA upgraded ON course.`AYSOID` = upgraded.`AYSOID`
    WHERE
        upgraded.`CertDate` IS NULL;
        
DROP TABLE IF EXISTS tmp_NRAC;

CREATE TEMPORARY TABLE tmp_NRAC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        LOWER(`CertificationDesc`) = ('National Referee Assessor Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_NRA;

CREATE TEMPORARY TABLE tmp_NRA SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    LOWER(`CertificationDesc`) = LOWER('National Referee Assessor');

INSERT INTO tmp_ref_upgrades SELECT DISTINCT
		course.`AYSOID`,
		course.`Name`,
		course.`First Name`,
		course.`Last Name`,
		course.`Address`,
		course.`City`,
		course.`State`,
		course.`Zip`,
		course.`Home Phone`,
		course.`Cell Phone`,
		course.`Email`,
		course.`Gender`,
		course.`CertificationDesc`,
		course.`CertDate`,
		course.`SAR`,
		course.`Section`,
		course.`Area`,
		course.`Region`, 
		course.`Membership Year`
    FROM
        tmp_NRAC course LEFT JOIN tmp_NRA upgraded ON course.`AYSOID` = upgraded.`AYSOID`
    WHERE
        upgraded.`CertDate` IS NULL;
        
DROP TABLE IF EXISTS tmp_RIC;

CREATE TEMPORARY TABLE tmp_RIC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        LOWER(`CertificationDesc`) = LOWER('Referee Instructor Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_RI;

CREATE TEMPORARY TABLE tmp_RI SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    LOWER(`CertificationDesc`) = LOWER('Referee Instructor');

INSERT INTO tmp_ref_upgrades SELECT DISTINCT
		course.`AYSOID`,
		course.`Name`,
		course.`First Name`,
		course.`Last Name`,
		course.`Address`,
		course.`City`,
		course.`State`,
		course.`Zip`,
		course.`Home Phone`,
		course.`Cell Phone`,
		course.`Email`,
		course.`Gender`,
		course.`CertificationDesc`,
		course.`CertDate`,
		course.`SAR`,
		course.`Section`,
		course.`Area`,
		course.`Region`, 
		course.`Membership Year`
    FROM
        tmp_RIC course LEFT JOIN tmp_RI upgraded ON course.`AYSOID` = upgraded.`AYSOID`
    WHERE
        upgraded.`CertDate` IS NULL;
        
DROP TABLE IF EXISTS tmp_ARIC;

CREATE TEMPORARY TABLE tmp_ARIC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        LOWER(`CertificationDesc`) = ('Advanced Referee Instructor Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_ARI;

CREATE TEMPORARY TABLE tmp_ARI SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    (`CertificationDesc`) = ('Advanced Referee Instructor');

INSERT INTO tmp_ref_upgrades SELECT DISTINCT
		course.`AYSOID`,
		course.`Name`,
		course.`First Name`,
		course.`Last Name`,
		course.`Address`,
		course.`City`,
		course.`State`,
		course.`Zip`,
		course.`Home Phone`,
		course.`Cell Phone`,
		course.`Email`,
		course.`Gender`,
		course.`CertificationDesc`,
		course.`CertDate`,
		course.`SAR`,
		course.`Section`,
		course.`Area`,
		course.`Region`, 
		course.`Membership Year`
    FROM
        tmp_ARIC course LEFT JOIN tmp_ARI upgraded ON course.`AYSOID` = upgraded.`AYSOID`
    WHERE
        upgraded.`CertDate` IS NULL;
        

DROP TABLE IF EXISTS tmp_RIEC;

CREATE TEMPORARY TABLE tmp_RIEC SELECT * FROM
    (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_refcerts`
    WHERE
        (`CertificationDesc`) = ('Advanced Referee Instructor Course')
    GROUP BY `AYSOID` , `Membership Year` DESC) ordered) ranked
WHERE
    rank = 1;

DROP TABLE IF EXISTS tmp_RIE;

CREATE TEMPORARY TABLE tmp_RIE SELECT `AYSOID`, `CertDate` FROM
    crs_refcerts
WHERE
    (`CertificationDesc`) = ('Advanced Referee Instructor');

INSERT INTO tmp_ref_upgrades SELECT DISTINCT
		course.`AYSOID`,
		course.`Name`,
		course.`First Name`,
		course.`Last Name`,
		course.`Address`,
		course.`City`,
		course.`State`,
		course.`Zip`,
		course.`Home Phone`,
		course.`Cell Phone`,
		course.`Email`,
		course.`Gender`,
		course.`CertificationDesc`,
		course.`CertDate`,
		course.`SAR`,
		course.`Section`,
		course.`Area`,
		course.`Region`, 
		course.`Membership Year`
    FROM
        tmp_RIEC course LEFT JOIN tmp_RIE upgraded ON course.`AYSOID` = upgraded.`AYSOID`
    WHERE
        upgraded.`CertDate` IS NULL;
        
DROP TABLE IF EXISTS crs_rpt_ref_upgrades;

CREATE TABLE crs_rpt_ref_upgrades SELECT DISTINCT * FROM
    tmp_ref_upgrades
ORDER BY FIELD(`CertificationDesc`,
        'National Referee Course',
        'Advanced Referee Course',
        'Intermediate Referee Course',
        'Referee Assessor Course',
        'National Referee Assessor Course',
        'Referee Instructor Course',
        'Advanced Referee Instructor Course',
        'Referee Instructor Evaluator Course'), `Section` , `Area` , `Region` , `CertDate`;
        
END$$

DROP PROCEDURE IF EXISTS `RefreshRefNoCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshRefNoCerts` ()  SQL SECURITY INVOKER
BEGIN   
SET sql_mode = "STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION";
 
DROP TABLE IF EXISTS crs_rpt_nocerts;

CREATE TABLE crs_rpt_nocerts SELECT 
    *
FROM
    (SELECT DISTINCT
        `AYSOID`,
            `Name`,
            `First Name`,
            `Last Name`,
            `Address`,
            `City`,
            `State`,
            `Zip`,
            `Home Phone`,
            `Cell Phone`,
            `Email`,
            `Gender`,
            `Volunteer Role`,
            `CertificationDesc`,
            `CertDate`,
            `SAR`,
            `Section`,
            `Area`,
            `Region`,
            `Membership Year`
    FROM
        (SELECT 
        *,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM
        (SELECT 
        *
    FROM
        `crs_certs`
    GROUP BY `AYSOID` , `Membership Year` DESC) ranked
    ) ordered
    WHERE
        rank = 1) s1
WHERE `Volunteer Role` LIKE '%Referee%' 
	AND `CertificationDesc` = '' 
    AND NOT `Volunteer Role` LIKE '%General Volunteer%'
ORDER BY `Section`, `Area`, `Region`;

END$$

DROP PROCEDURE IF EXISTS `RefreshSafeHavenCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshSafeHavenCerts` ()  BEGIN
SET @id:= 0;

DROP TABLE IF EXISTS crs_rpt_safehaven;
SET @s = CONCAT("
CREATE TABLE crs_rpt_safehaven SELECT 
		`Program Name`,
		`Membership Year`,
		`Volunteer Role`,
		`AYSOID`,
		`Name`,
		`First Name`,
		`Last Name`,
		`Address`,
		`City`,
		`State`,
		`Zip`,
		`Home Phone`,
		`Cell Phone`,
		`Email`,
		`Gender`,
		`CertificationDesc`,
		`CertDate`,
		`SAR`,
		`Section`,
		`Area`,
		`Region`
    FROM
        (SELECT 
			*,
            @rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
            @id:=`AYSOID`
    FROM (SELECT 
    	sh.`Program Name`,
		sh.`Membership Year`,
		sh.`Volunteer Role`,
		sh.`AYSOID`,
		sh.`Name`,
		sh.`First Name`,
		sh.`Last Name`,
		sh.`Address`,
		sh.`City`,
		sh.`State`,
		sh.`Zip`,
		sh.`Home Phone`,
		sh.`Cell Phone`,
		sh.`Email`,
		sh.`Gender`,
		sh.`CertificationDesc`,
		sh.`CertDate`,
		sh.`SAR`,
		sh.`Section`,
		sh.`Area`,
		sh.`Region`
    FROM 
        crs_shcerts sh RIGHT JOIN crs_rpt_hrc hrc USING (`AYSOID`)
	WHERE sh.`CertificationDesc` LIKE '%AYSOs Safe Haven'
		OR sh.`CertificationDesc` LIKE '%Refugio Seguro de AYSO'
    ORDER BY sh.`CertDate` DESC, sh.`Membership Year` DESC) ordered
    GROUP BY `AYSOID`, `CertDate`, `Membership Year`) ranked
WHERE
    rank = 1
ORDER BY `Section`, `Area`, `Region`, `Last Name`, `First Name`");
    
PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

ALTER TABLE crs_rpt_safehaven ADD INDEX (`AYSOID`);
END$$

DROP PROCEDURE IF EXISTS `RefreshUnregisteredReferees`$$
CREATE DEFINER=`root`@`%` PROCEDURE `RefreshUnregisteredReferees` ()  BEGIN
SET @dfields := "'National Referee', 'National 2 Referee', 'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 'z-Online Regional Referee without Safe Haven', 'Z-Online Regional Referee', 'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 'U-8 Official & Safe Haven Referee', 'Z-Online Safe Haven Referee', 'Safe Haven Referee', ''";

SET @maxMY = (SELECT MAX(`Membership Year`)
FROM
    ayso1ref_services.crs_rpt_hrc);

DROP TABLE IF EXISTS crs_rpt_unregistered_refs;

CREATE TABLE crs_rpt_unregistered_refs SELECT * FROM
    (SELECT 
        *
    FROM
        ayso1ref_services.crs_rpt_hrc
    WHERE
        `Membership Year` < @maxMY) unreg
ORDER BY `Section` , `Area` , `Region` , FIELD(`CertificationDesc`,
        'National Referee',
        'National 2 Referee',
        'Advanced Referee',
        'Intermediate Referee',
        'Regional Referee',
        'Regional Referee & Safe Haven Referee',
        'z-Online Regional Referee without Safe Haven',
        'Z-Online Regional Referee',
        'Assistant Referee',
        'Assistant Referee & Safe Haven Referee',
        'U-8 Official',
        'U-8 Official & Safe Haven Referee',
        'Z-Online Safe Haven Referee',
        'Safe Haven Referee',
        '');
END$$

DROP PROCEDURE IF EXISTS `rs_ar1AssignmentMap`$$
CREATE DEFINER=`root`@`%` PROCEDURE `rs_ar1AssignmentMap` (IN `projectKey` VARCHAR(45), `has4th` VARCHAR(45))  BEGIN
SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

SET @s = CONCAT("
SELECT ar1 as name, assignor, date, time, division, 0 as crCount, COUNT(ar1) as ar1Count, 0 as ar2Count", has4th, "
FROM `rs_games`
WHERE `projectKey` = '", projectKey, "' 
	AND `ar1` <> ''
GROUP BY `ar1`, `date`, `division`
");

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `rs_ar2AssignmentMap`$$
CREATE DEFINER=`root`@`%` PROCEDURE `rs_ar2AssignmentMap` (IN `projectKey` VARCHAR(45), IN `has4th` VARCHAR(45))  BEGIN
SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

SET @s = CONCAT("
SELECT ar2 as name, assignor, date, time, division, 0 as crCount,  0 as ar1Count, COUNT(ar2) as ar2Count", has4th, "
FROM `rs_games`
WHERE `projectKey` = '", projectKey, "' 
	AND `ar2` <> ''
GROUP BY `ar2`, `date`, `division`
");

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `rs_crAssignmentMap`$$
CREATE DEFINER=`root`@`%` PROCEDURE `rs_crAssignmentMap` (IN `projectKey` VARCHAR(45), IN `has4th` VARCHAR(45))  BEGIN
SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

SET @s = CONCAT("
SELECT cr as name, assignor, date, time, division, COUNT(cr) as crCount, 0 as ar1Count, 0 as ar2Count", has4th, "
FROM `rs_games`
WHERE `projectKey` = '", projectKey, "' 
	AND `cr` <> ''
GROUP BY `cr`, `date`, `division`
");

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `rs_r4thAssignmentMap`$$
CREATE DEFINER=`root`@`%` PROCEDURE `rs_r4thAssignmentMap` (IN `projectKey` VARCHAR(45), `has4th` VARCHAR(45))  BEGIN
SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

SET @s = CONCAT("
SELECT cr as name, assignor, date, time, division, 0 as crCount, 0 as ar1Count, 0 as ar2Count, COUNT(r4th) as r4thCount
FROM `rs_games`
WHERE `projectKey` = '", projectKey, "' 
	AND `r4th` <> ''
GROUP BY `r4th`, `date`, `division`
");

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;
END$$

DROP PROCEDURE IF EXISTS `UpdateCompositeMYCerts`$$
CREATE DEFINER=`root`@`%` PROCEDURE `UpdateCompositeMYCerts` ()  BEGIN

DROP TABLE IF EXISTS `tmp_composite`;
SET @s = CONCAT("
CREATE TEMPORARY TABLE `tmp_composite` SELECT * FROM
		(SELECT 
			AYSOID,
				`Name`,
				`First Name`,
				`Last Name`,
				Address,
				City,
				State,
				Zip,
				`Home Phone`,
				`Cell Phone`,
				Email,
				`Gender`,
				CertificationDesc,
				CertDate,
				SAR,
				Section,
				Area,
				Region,
				`Membership Year`
		FROM
			(SELECT 
			*,
				@rank:=IF(@id = `AYSOID`, @rank + 1, 1) AS rank,
				@id:=`AYSOID`
		FROM
			(SELECT 
			*
		FROM (
		
		SELECT 
			AYSOID,
				`Name`,
				`First Name`,
				`Last Name`,
				Address,
				City,
				State,
				REPLACE(`Zip`, '\'', '') AS Zip,
				`Home Phone`,
				`Cell Phone`,
				Email,
				`Gender`,
				CertificationDesc,
				CertDate,
				SAR,
				Section,
				Area,
				Region,
				`Membership Year`
		FROM
			.`eAYSO.MY2017.certs_highestRefCert` 
	UNION SELECT 
			AYSOID,
				`Name`,
				`First Name`,
				`Last Name`,
				Address,
				City,
				State,
				REPLACE(`Zip`, '\'', '') AS Zip,
				`Home Phone`,
				`Cell Phone`,
				Email,
				`Gender`,
				CertificationDesc,
				CertDate,
				SAR,
				Section,
				Area,
				Region,
				`Membership Year`
		FROM
			.`eAYSO.MY2016.certs_highestRefCert` 
	UNION SELECT 
			AYSOID,
				`Name`,
				`First Name`,
				`Last Name`,
				Address,
				City,
				State,
				Zip,
				`Home Phone`,
				`Cell Phone`,
				Email,
				`Gender`,
				CertificationDesc,
				CertDate,
				SAR,
				Section,
				Area,
				Region,
				`Membership Year`
		FROM
			.crs_rpt_hrc) hrc
		GROUP BY `AYSOID` , Field(`Membership Year`, 'MY2018', 'MY2017', 'MY2016'), `Section`, `Area`) ordered) ranked
		ORDER BY AYSOID, `Membership Year` DESC) composite;
");

PREPARE stmt FROM @s;

EXECUTE stmt;

DEALLOCATE PREPARE stmt;

DROP TABLE IF EXISTS `s1_composite_my_certs`;
CREATE TABLE s1_composite_my_certs SELECT DISTINCT * FROM (
		SELECT 
			AYSOID,
				`Name`,
				`First Name`,
				`Last Name`,
				Address,
				City,
				State,
				Zip,
				`Home Phone`,
				`Cell Phone`,
				Email,
				`Gender`,
				CertificationDesc,
				CertDate,
				SAR,
				Section,
				Area,
				Region,
				`Membership Year`
		FROM
			(SELECT 
			*,
				@rankID:=IF(@id = `AYSOID`, @rankID + 1, 1) AS rankID,
				@id:=`AYSOID`,
				@rankMY:=IF(@my=`Membership Year` OR @rankID=1, 1, @rankMY + 1) AS rankMY,
				@my:=`Membership Year`
			FROM tmp_composite) ranked
            
		WHERE rankMY = 1) s1;
        
END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `multiTrim`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `multiTrim` (`string` TEXT, `remove` CHAR(63)) RETURNS TEXT CHARSET latin1 BEGIN
  
  WHILE length(string)>0 and remove LIKE concat('%',substring(string,-1),'%') DO
    set string = substring(string,1,length(string)-1);
  END WHILE;

  
  WHILE length(string)>0 and remove LIKE concat('%',left(string,1),'%') DO
    set string = substring(string,2);
  END WHILE;

  RETURN string;
END$$

DROP FUNCTION IF EXISTS `PROPER_CASE`$$
CREATE DEFINER=`root`@`%` FUNCTION `PROPER_CASE` (`str` VARCHAR(255)) RETURNS VARCHAR(255) CHARSET utf8 BEGIN 
  DECLARE c CHAR(1); 
  DECLARE s VARCHAR(255); 
  DECLARE i INT DEFAULT 1; 
  DECLARE bool INT DEFAULT 1; 
  DECLARE punct CHAR(18) DEFAULT ' ()[]{},.-_!@;:?/';
  SET s = LCASE( str ); 
  WHILE i <= LENGTH( str ) DO 
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
END$$

DROP FUNCTION IF EXISTS `SPLIT_STRING`$$
CREATE DEFINER=`root`@`%` FUNCTION `SPLIT_STRING` (`str` VARCHAR(255), `delim` VARCHAR(12), `pos` INT) RETURNS VARCHAR(255) CHARSET utf8 BEGIN
RETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(str, delim, pos),
       LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1),
       delim, '');
RETURN 1;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `crs_rpt_lastUpdate`
--

DROP TABLE IF EXISTS `crs_rpt_lastUpdate`;
CREATE TABLE `crs_rpt_lastUpdate` (
  `timestamp` datetime NOT NULL DEFAULT '1901-01-01 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `crs_rpt_lastUpdate`
--
ALTER TABLE `crs_rpt_lastUpdate`
  ADD UNIQUE KEY `timestamp` (`timestamp`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
