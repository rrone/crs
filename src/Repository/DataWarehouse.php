<?php

namespace App\Repository;

use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
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
     * @param string $projectDir
     */
    public function __construct(string $projectDir)
    {
        global $kernel;

        $this->conn = $kernel->getContainer()->get('doctrine.dbal.default_connection');
        $this->root = $projectDir;
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

    //User fetchAll functions

    /**
     * @return object
     */
    public function getAllUsers()
    {

        $users = $this->conn->fetchAll('SELECT * FROM crs_users');

        return $users;
    }

    /**
     * @param $name
     * @return null|object
     */
    public function getUserByName($name)
    {
        if (empty($name)) {
            return null;
        }

        $user = $this->conn->fetchAll("SELECT * FROM crs_users WHERE `name` LIKE '$name'");

        return $this->getZero($user);

    }


    /**
     * @param $hash
     * @return null|object
     */
    public function getUserByPW($hash)
    {
        if (empty($hash)) {
            return null;
        }

        $user = $this->conn->fetchAll("SELECT * FROM crs_users WHERE `hash` LIKE $hash");

        return $this->getZero($user);

    }

    /**
     * @param $user
     * @return null
     */
//    public function setUser($user)
//    {
//        if (empty($user)) {
//            return null;
//        }
//
//        $u = $this->getUserByName($user['name']);
//        if (empty($u)) {
//            $newUser = array(
//                'name' => $user['name'],
//                'enabled' => $user['enabled'],
//                'hash' => $user['hash'],
//            );
//
//            $this->conn->fetchAll('users')
//                ->insert([$newUser]);
//
//            $newUser = $this->getUserByName($newUser['name']);
//
//            return $newUser->id;
//
//        } else {
//            $hash = $user['hash'];
//
//            $this->conn->fetchAll('users')
//                ->where('id', $u->id)
//                ->update(
//                    [
//                        'hash' => $hash,
//                    ]
//                );
//
//            return $u->id;
//        }
//
//    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getHighestRefCerts($userKey, $limit = self::BIGINT)
    {
        $results = $this->conn->fetchAll(
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

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getRefAssessors($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_ra 
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
        "
        );

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getRefNationalAssessors($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '';
        }

        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_nra 
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
        "
        );

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getRefInstructors($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_ri 
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', '') , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
        "
        );

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getRefInstructorEvaluators($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_rie 
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD
                (`RefereeInstructorCert`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', 'Referee Instructor') , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
        "
        );

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getRefUpgradeCandidates($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_ref_upgrades
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Course', 'Advanced Referee Course', 'Intermediate Referee Course', 'National Referee Assessor Course', 'Referee Assessor Course', 'Advanced Referee Instructor Course', 'Intermediate Referee Instructor Course', 'Regional Referee Instructor Course', 'Referee Instructor Course', '') , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
            "
        );

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getUnregisteredRefs($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
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

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getRefsConcussion($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_ref_cdc
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section` , `Area` , ABS(`Region`) , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
        "
        );

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getSafeHavenRefs($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_safehaven
            WHERE `sar` LIKE '%$userKey%' OR `area` = ''
            ORDER BY `Section`, `Area` , ABS(`Region`) , `Last Name` , `First Name` , `AYSOID`
            LIMIT $limit
        "
        );

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getCompositeRefCerts($userKey, $limit = self::BIGINT)
    {
        $results = $this->conn->fetchAll(
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

        return $results;
    }

    public function getCompositeRefCertsFile()
    {
        $results = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_ref_certs_file
            ");
        $file = realpath(__DIR__ . '/../../var/xlsx/' . $results[0]['file']);

        return $file;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getRefsWithNoBSCerts($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->conn->fetchAll(
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

        return $results;
    }

    /**
     * @return mixed
     */
    public function getReports()
    {
        return $this->conn->fetchAll(
            "
            SELECT * FROM crs_reports
            WHERE `show` = 1
            ORDER BY `seq`
        "
        );
    }

    /**
     * @return mixed
     */
    public function getReportNotes()
    {
        return $this->conn->fetchAll(
            "
            SELECT * FROM crs_report_notes
            ORDER BY `seq`
        "
        );
    }


    /**
     * @return mixed
     */
    public function getUpdateTimestamp()
    {
        $ts = $this->conn->fetchAll(
            "
            SELECT * FROM crs_rpt_lastUpdate
            ORDER BY `timestamp`
            LIMIT 1
            "
        );

        $updated = $this->getZero($ts)->timestamp;

        return $updated;
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
     * @throws DBALException
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
     */
    public function getAccessLog()
    {
        return $this->conn->fetchAll("SELECT * FROM crs_log");
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
//        $ts = $this->conn->fetchAll('log')
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
