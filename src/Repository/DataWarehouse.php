<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine;
use Exception;

/**
 * Class DataWarehouse
 * @package App\Controller
 */
class DataWarehouse
{

    /**
     * @var Connection $conn
     */
    protected $conn;

    protected $root;

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
    public function bigLimit()
    {
        return self::BIGINT;
    }

    /**
     * @param $elem
     * @return null|object
     */
    private function getZero($elem)
    {
        return isset($elem[0]) ? (object)$elem[0] : null;
    }

    //User fetchAllAssociative functions

    /**
     * @return array
     * @throws Doctrine\DBAL\Exception
     */
    public function getAllUsers()
    {

        return $this->conn->fetchAllAssociative('SELECT * FROM crs_users');
    }

    /**
     * @param $name
     * @return null|object
     * @throws Doctrine\DBAL\Exception
     */
    public function getUserByName($name)
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
     * @throws Doctrine\DBAL\Driver\Exception|Doctrine\DBAL\Exception
     */
    public function getUserByHash($hash)
    {
        if (empty($hash)) {
            return null;
        }

        $stmt = $this->conn->prepare("SELECT * FROM crs_users WHERE `hash` LIKE ?");
        $stmt->execute([$hash]);
        $user = $stmt->fetchAllAssociative();

        return $this->getZero($user);

    }

    /**
     * @param $user
     * @return null
     * @throws Doctrine\DBAL\Driver\Exception|Doctrine\DBAL\Exception
     */
    public function setUser($user)
    {
        if(empty($user['name'])){
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
            $stmt->execute([$newUser->name, $newUser->enabled, $newUser->hash]);
            $newUser = $this->getUserByName($newUser->name);

            return isset($newUser) ? $newUser->id : null;

        } else {
            $stmt = $this->conn->prepare("UPDATE crs_users SET `hash` = ? WHERE `id` = ?");
            $stmt->execute([$user['hash'], (int)$u->id]);

            return $u->id;
        }
    }

    /**
     * @param $user
     * @return null
     * @throws Doctrine\DBAL\Driver\Exception
     * @throws Doctrine\DBAL\Exception
     */
    public function removeUser($user)
    {
        if (empty($user)) {
            return null;
        }
        $stmt = $this->conn->prepare("DELETE FROM crs_users WHERE `name` = ?");
        $stmt->execute([$user->name]);

        return null;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getHighestRefCerts($userKey, $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * from crs_rpt_hrc
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee','National 2 Referee',
            'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee',
                                'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official',
                                'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`
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
    public function getRefAssessors($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_ra
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `Last Name` , `First Name` , `AYSOID`
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
    public function getRefNationalAssessors($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_nra
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `Last Name` , `First Name` , `AYSOID`
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
    public function getRefInstructors($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_ri
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', '') , `Last Name` , `First Name` , `AYSOID`
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
    public function getRefInstructorEvaluators($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_rie
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD
                (`RefereeInstructorCert`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', 'Referee Instructor') , `Last Name` , `First Name` , `AYSOID`
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
    public function getRefUpgradeCandidates($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_ref_upgrades
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Course', 'Advanced Referee Course', 'Intermediate Referee Course', 'National Referee Assessor Course', 'Referee Assessor Course', 'Advanced Referee Instructor Course', 'Intermediate Referee Instructor Course', 'Regional Referee Instructor Course', 'Referee Instructor Course', '') , `Last Name` , `First Name` , `AYSOID`
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
    public function getUnregisteredRefs($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM `crs_rpt_unregistered_refs`
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`),
                    FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee',
                    'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee',
                    'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official',
                    'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`
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
    public function getRefsConcussion($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_ref_cdc
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section` , `Area` , ABS(`Region`) , `Last Name` , `First Name` , `AYSOID`
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
    public function getSafeHavenRefs($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_safehaven
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area` , ABS(`Region`) , `Last Name` , `First Name` , `AYSOID`
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
    public function getCompositeRefCerts($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_ref_certs
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`),
                FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee',
                'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee',
                'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official',
                'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`
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
    public function getRefsWithNoBSCerts($userKey = '', $limit = self::BIGINT)
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_rpt_nocerts
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`),
                    FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee',
                    'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee',
                    'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official',
                    'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
            "
        );
    }

    /**
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getReports()
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_reports
            WHERE `show` = 1
            ORDER BY `seq`
        "
        );
    }

    /**
     * @return mixed
     * @throws Doctrine\DBAL\Exception
     */
    public function getReportNotes()
    {
        return $this->conn->fetchAllAssociative(
            "
            SELECT * FROM crs_report_notes
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
            SELECT * FROM crs_rpt_lastUpdate
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
     * @return mixed[]
     * @throws Doctrine\DBAL\Exception
     */
    public function getAccessLog()
    {
        return $this->conn->fetchAllAssociative("SELECT * FROM crs_log");
    }

    /**
     *
     */
//    public function showVariables()
//    {
//        return $this->conn->getConnection();
//    }

//Log reader

    /**
     * @param $key
     * @param $userName
     * @return string|null
     * @throws Exception
     */
//    public function getLastLogon($key, $userName)
//    {
//        $timestamp = null;
//
//        $ts = $this->conn->fetchAllAssociative('log')
//            ->where(
//                [
//                    ['projectKey', 'like', $key],
//                    ['note', 'like', "$userName: CRS logon%"],
//                ]
//            )
//            ->orderBy('timestamp', 'desc')
//            ->limit(1)
//            ->get();
//
//        $ts = $this->getZero($ts);
//
//        if (!empty($ts)) {
//            $utc = new DateTime($ts->timestamp, new DateTimeZone('UTC'));
//            $time = $utc->setTimezone(new DateTimeZone('America/Los_Angeles'));
//            $timestamp = $time->format('Y-M-j H:i');
//        }
//
//        return $timestamp;
//    }

}
