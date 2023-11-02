<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine;
use Exception;

define("CurrentMY", "MY2019");

/**
 * Class DataWarehouse
 * @package App\Controller
 */
class DataWarehouse
{

    /**
     * @var Connection $conn
     */
    protected Connection $conn;

    /**
     * @const BIGINT
     */
    const BIGINT = 9223372036854775807;

    /**
     * DataWarehouse constructor.
     * @param Connection $connection
     * @throws Exception
     */
    public function __construct(Connection $connection)
    {
        $this->conn = $connection;
    }

    /**
     * @return int
     */
    public function bigLimit(): int
    {
        return self::BIGINT;
    }

    /**
     * @param $elem
     * @return null|object
     */
    private function getZero($elem): ?object
    {
        return isset($elem[0]) ? (object)$elem[0] : null;
    }

    //User fetchAllAssociative functions

    /**
     * @return array
     * @throws Doctrine\DBAL\Exception
     */
    public function getAllUsers(): array
    {

        return $this->conn->fetchAllAssociative('SELECT * FROM crs_users');
    }

    /**
     * @param $name
     * @return null|object
     * @throws Doctrine\DBAL\Exception
     */
    public function getUserByName($name): ?object
    {
        if (empty($name)) {
            return null;
        }

        $user = $this->conn->fetchAllAssociative("SELECT * FROM crs_users WHERE `name` LIKE '$name'");

        return $this->getZero($user);

    }

    /**
     * @param $hash
     * @return null|object
     * @throws Doctrine\DBAL\Exception
     */
    public function getUserByHash($hash): ?object
    {
        if (empty($hash)) {
            return null;
        }

        $stmt = $this->conn->prepare("SELECT * FROM crs_users WHERE `hash` LIKE ?");
        $user = $stmt->executeQuery([$hash])->fetchAllAssociative();

        return $this->getZero($user);

    }

    /**
     * @param $user
     * @return null
     * @throws Doctrine\DBAL\Exception
     */
    public function setUser($user)
    {
        if (empty($user['name'])) {
            return null;
        }

        $u = $this->getUserByName($user['name']);
        if (empty($u)) {
            $newUser = (object)array(
                'name' => $user['name'],
                'enabled' => $user['enabled'],
                'hash' => $user['hash'],
            );

            $stmt = $this->conn->prepare("INSERT INTO crs_users (`name`, `enabled`, `hash`) VALUES (?, ?, ?)");
            $stmt->executeQuery([$newUser->name, $newUser->enabled, $newUser->hash]);
            $newUser = $this->getUserByName($newUser->name);

            return isset($newUser) ? $newUser->id : null;

        } else {
            $stmt = $this->conn->prepare("UPDATE crs_users SET `hash` = ? WHERE `id` = ?");
            $stmt->executeQuery([$user['hash'], (int)$u->id]);

            return $u->id;
        }
    }

    /**
     * @param $user
     * @return null
     * @throws Doctrine\DBAL\Exception
     */
    public function removeUser($user)
    {
        if (empty($user)) {
            return null;
        }
        $stmt = $this->conn->prepare("DELETE FROM crs_users WHERE `name` = ?");
        $stmt->executeQuery([$user->name]);

        return null;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefAssessors($userKey = '', int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_ra
            WHERE `sar` LIKE '%$userKey%' AND `MY` >= '$MY'
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `Last_Name` , `First_Name`
            LIMIT $limit
        "
        );
    }

    /**
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefNationalAssessors(): array
    {
        $MY = CurrentMY;

        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ra
            WHERE `CertificationDesc` = 'National Referee Assessor' AND `MY` >= '$MY'
            ORDER BY `Section`, `Area`, ABS(`Region`), `Last_Name` , `First_Name`
        "
        );
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefInstructors($userKey = '', int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ri
            WHERE `sar` LIKE '%$userKey%' AND `MY` >= '$MY'
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', '') , `Last_Name` , `First_Name` , `AYSOID`
            LIMIT $limit
        "
        );
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefInstructorEvaluators($userKey = '', int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;
        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_rie
            WHERE `sar` LIKE '%$userKey%' AND `MY` >= '$MY'
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD
                (`InstructorDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', 'Referee Instructor') , `Last_Name` , `First_Name`
            LIMIT $limit
        "
        );
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefUpgradeCandidates($userKey = '', int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_upgrades
            WHERE `sar` LIKE '%$userKey%' AND `MY` >= '$MY'
            ORDER BY LEFT(`SAR`,4), FIELD(`Training`, 'National Referee Course', 'Advanced Referee Course', 'Intermediate Referee Course', 'National Referee Assessor Course', 'Referee Assessor Course', 'Advanced Referee Instructor Course', 'Intermediate Referee Instructor Course', 'Regional Referee Instructor Course', '') , `Last_Name` , `TrainingDate`
            LIMIT $limit
            "
        );
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getUnregisteredRefs($userKey = '', int $limit = self::BIGINT): array
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_unregistered_refs
            WHERE `sar` LIKE '%$userKey%'
            ORDER BY `Section`, `Area`, ABS(`Region`),
                    FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee',
                    'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee',
                    'Assistant Referee', 'Assistant Referee & Safe Haven Referee', '8U Official',
                    '8U Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
            "
        );
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getConcussionRefs($userKey = '', int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        $results = $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_certs
            WHERE (`sar` LIKE '%$userKey%' ) AND `Concussion_Awareness_Date` IS NULL AND `MY` >= '$MY'
            ORDER BY `Section` , `Area` , ABS(`Region`) , `Last_Name` , `First_Name` , `AYSOID`
            LIMIT $limit
        "
        );

        foreach ($results as &$result) {
            unset($result['Safe_Haven_Date']);
            unset($result['Sudden_Cardiac_Arrest_Date']);
            unset($result['SafeSport_Date']);
            unset($result['LiveScan_Date']);
            unset($result['Risk_Status']);
            unset($result['Risk_Expire_Date']);
        }

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getSafeHavenRefs($userKey = '', int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_certs
            WHERE (`sar` LIKE '%$userKey%' ) AND `Safe_Haven_Date` IS NULL AND `MY` >= '$MY'
            ORDER BY `Section`, `Area` , ABS(`Region`) , `Last_Name` , `First_Name` , `AYSOID`
            LIMIT $limit
        "
        );
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getCompositeRefCerts($userKey = '', int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_certs
            WHERE `sar` LIKE '%$userKey%' AND `MY` >= '$MY'
            ORDER BY `Section`, `Area`, ABS(`Region`),
                FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee',
                'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee',
                'Assistant Referee', 'Assistant Referee & Safe Haven Referee', '8U Official',
                '8U Official & Safe Haven Referee', '') , `Last_Name` , `First_Name` , `AYSOID`
            LIMIT $limit
            "
        );
    }

    /**
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getReports(): array
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_reports
            WHERE `show` = 1
            ORDER BY `seq`
        "
        );
    }

    /**
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getReportNotes(): array
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_report_notes
            WHERE `enabled` IS TRUE
            ORDER BY `seq`
        "
        );
    }

    /**
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getUpdateTimestamp()
    {
        $ts = $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_lastUpdate
            ORDER BY `timestamp`
            LIMIT 1
            "
        );

        return $this->getZero($ts)->timestamp;
    }

//    public function getTableHeaders($tableName)
//    {
//        if (is_null($tableName)) {
//            return null;
//        }
//
//        $results = $this->conn::schema()->getColumnListing($tableName);
//
//        return $results;
//    }

//Log writer

    /**
     * @param $key
     * @param $msg
     * @return null
     * @throws Exception
     */
    public function logInfo($key, $msg)
    {
        $data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'projectKey' => $key,
            'note' => $msg,
        ];

        $this->conn->insert('crs_log', $data);

        return null;
    }

    /**
     * @return array
     * @throws Doctrine\DBAL\Exception
     */
    public function getAccessLog(): array
    {
        return $this->conn->fetchAllAssociative("SELECT * FROM crs_log");
    }

    /**
     * @param mixed $userKey
     * @param int $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function SuddenCardiacArrestRefs($userKey, int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        $results = $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_certs
            WHERE (`sar` LIKE '%$userKey%' ) AND `Sudden_Cardiac_Arrest_Date` IS NULL AND `MY` >= '$MY'
            ORDER BY `Section`, `Area` , ABS(`Region`) , `Last_Name` , `First_Name`
            LIMIT $limit
        "
        );

        foreach ($results as &$result) {
            unset($result['Safe_Haven_Date']);
            unset($result['Concussion_Awareness_Date']);
            unset($result['SafeSport_Date']);
            unset($result['SafeSport_Expire_Date']);
            unset($result['LiveScan_Date']);
            unset($result['Risk_Status']);
            unset($result['Risk_Expire_Date']);
        }

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param int $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getSafeSportRefs($userKey, int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        $results = $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_certs
            WHERE (`sar` LIKE '%$userKey%' ) AND `SafeSport_Date` <> '' AND `MY` >= '$MY'
            ORDER BY `Section`, `Area` , ABS(`Region`) , `Last_Name` , `First_Name`
            LIMIT $limit
        "
        );

        foreach ($results as &$result) {
            unset($result['Safe_Haven_Date']);
            unset($result['Concussion_Awareness_Date']);
            unset($result['Sudden_Cardiac_Arrest_Date']);
            unset($result['LiveScan_Date']);
            unset($result['Risk_Status']);
            unset($result['Risk_Expire_Date']);
        }

        return $results;

    }

    /**
     * @param mixed $userKey
     * @param int $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getSafeSportExpirationRefs($userKey, int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        $results = $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_rssx
            WHERE (`sar` LIKE '%$userKey%' ) AND `SafeSport_Date` <> '' AND `MY` >= '$MY'
            ORDER BY `Section`, `Area`, ABS(`Region`), `Last_Name`, `First_Name`
            LIMIT $limit
        "
        );

        foreach ($results as &$result) {
            unset($result['AYSOID']);
            unset($result['Cell_Phone']);
            unset($result['Gender']);
            unset($result['Safe_Haven_Date']);
            unset($result['Concussion_Awareness_Date']);
            unset($result['Sudden_Cardiac_Arrest_Date']);
            unset($result['LiveScan_Date']);
            unset($result['Risk_Status']);
            unset($result['Risk_Expire_Date']);
        }

        return $results;

    }

    /**
     * @param mixed $userKey
     * @param int $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getLiveScanRefs($userKey, int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        $results = $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_certs
            WHERE (`sar` LIKE '%$userKey%' ) AND `LiveScan_Date` IS NULL AND `MY` >= '$MY'
            ORDER BY `Section`, `Area` , ABS(`Region`) , `Last_Name` , `First_Name`
            LIMIT $limit
        "
        );

        foreach ($results as &$result) {
            unset($result['Safe_Haven_Date']);
            unset($result['Concussion_Awareness_Date']);
            unset($result['Sudden_Cardiac_Arrest_Date']);
            unset($result['SafeSport_Date']);
            unset($result['SafeSport_Expire_Date']);
            unset($result['Risk_Status']);
            unset($result['Risk_Expire_Date']);
        }

        return $results;

    }

    /**
     * @param mixed $userKey
     * @param int $limit
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getExpiredRiskRefs($userKey, int $limit = self::BIGINT): array
    {
        $MY = CurrentMY;

        $results = $this->conn->fetchAllAssociative(
            "
            SELECT *
            FROM crs_rpt_ref_certs
            WHERE (`sar` LIKE '%$userKey%' ) AND `Risk_Status` IN ('None', 'Expired', NULL) AND `MY` >= '$MY'
            ORDER BY `Section`, `Area` , ABS(`Region`) , `Last_Name` , `First_Name`
            LIMIT $limit
        "
        );

        foreach ($results as &$result) {
            unset($result['AYSOID']);
            unset($result['Cell_Phone']);
            unset($result['Gender']);
            unset($result['Safe_Haven_Date']);
            unset($result['Concussion_Awareness_Date']);
            unset($result['Sudden_Cardiac_Arrest_Date']);
            unset($result['SafeSport_Date']);
            unset($result['SafeSport_Expire_Date']);
            unset($result['LiveScan_Date']);
        }

        return $results;

    }

    /**
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefAssessorsReport(): array
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT `SAR`,`CertificationDesc`,`First_Name`,`Last_Name`,`City`,`State`,`Email`
            FROM crs_rpt_ra
            WHERE `Current` <> ''
            ORDER BY FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `SAR`, `Last_Name` , `First_Name`
        "
        );
    }

    /**
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefInstructorsReport(): array
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT `SAR`,`CertificationDesc`,`First_Name`,`Last_Name`,`City`,`State`,`Email`
            FROM crs_rpt_ri
            WHERE `Current` <> ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor') , `Last_Name` , `First_Name`
        "
        );
    }

    /**
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefInstructorEvaluatorsReport(): array
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT `SAR`,`InstructorDesc`,`First_Name`,`Last_Name`,`City`,`State`,`Email`
            FROM crs_rpt_rie
            WHERE `InstructorDesc` IN ('National Referee Instructor', 'Advanced Referee Instructor') AND `Current` <> ''
        "
        );
    }


    /**
     * @return array[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getRefNationalAssessorsReport(): array
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT `SAR`,`CertificationDesc`,`First_Name`,`Last_Name`,`City`,`State`,`Email`
            FROM crs_rpt_ra
            WHERE `CertificationDesc` = 'National Referee Assessor' AND `Current` <> ''
            ORDER BY `Section`, `Area`, ABS(`Region`), `Last_Name` , `First_Name`
        "
        );
    }

}
