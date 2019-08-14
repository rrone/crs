<?php

namespace App\Repository;

//use DateTime;
//use DateTimeZone;
use Doctrine\DBAL\Connection;

/**
 * Class DataWarehouse
 * @package App\Controller
 */
class DataWarehouse
{

    /**
     * @var Connection $connection
     */
    protected $db;

    /**
     * @const BIGINT
     */
    const BIGINT = 9223372036854775807;

    /**
     * DataWarehouse constructor.
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->db = $connection;
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
     * @return mixed[]
     */
    public function getAllUsers()
    {
        return $this->db->fetchAll('SELECT * FROM crs_users');
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

        $user = $this->db->fetchAll("SELECT * FROM crs_users WHERE `name` LIKE $name");

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

        $user = $this->db->fetchAll("SELECT * FROM crs_users WHERE `hash` LIKE $hash");

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
//            $this->db->fetchAll('users')
//                ->insert([$newUser]);
//
//            $newUser = $this->getUserByName($newUser['name']);
//
//            return $newUser->id;
//
//        } else {
//            $hash = $user['hash'];
//
//            $this->db->fetchAll('users')
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
        $results = $this->db->fetchAll("
SELECT * from crs_rpt_hrc 
WHERE `sar` LIKE '%$userKey%' OR `area` = ''
ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee','National 2 Referee',
'Advanced Referee', 'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 
                    'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 
                    'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`
LIMIT $limit 
            ");

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

        $results = $this->db->fetchAll("
        SELECT * crs_rpt_ra 
        WHERE `sar` LIKE '%$userKey%' OR `area` = ''
        ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `Last Name` , `First Name` , `AYSOID`
        LIMIT $limit
        ");

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

        $results = $this->db->fetchAll("
        SELECT * crs_rpt_nra 
        WHERE `sar` LIKE '%$userKey%' OR `area` = ''
        ORDER BY `Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Assessor', 'Referee Assessor', '') , `Last Name` , `First Name` , `AYSOID`
        LIMIT $limit
        ");

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

        $results = $this->db->fetchAll('rpt_ri')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section`, `Area`, ABS(`Region`), FIELD(`CertificationDesc`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', '') , `Last Name` , `First Name` , `AYSOID`")
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return Collection
     */
    public function getRefInstructorEvaluators($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->db->fetchAll('rpt_rie')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section`, `Area`, ABS(`Region`), FIELD(`RefereeInstructorCert`, 'National Referee Instructor', 'Advanced Referee Instructor', 'Intermediate Referee Instructor', 'Regional Referee Instructor', 'Referee Instructor') , `Last Name` , `First Name` , `AYSOID`")
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return Collection
     */
    public function getRefUpgradeCandidates($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->db->fetchAll('rpt_ref_upgrades')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section`, `Area`, ABS(`Region`),FIELD(`CertificationDesc`, 'National Referee Course', 'Advanced Referee Course', 'Intermediate Referee Course', 'National Referee Assessor Course', 'Referee Assessor Course', 'Advanced Referee Instructor Course', 'Intermediate Referee Instructor Course', 'Regional Referee Instructor Course', 'Referee Instructor Course', '') , `Last Name` , `First Name` , `AYSOID`")
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return Collection
     */
    public function getUnregisteredRefs($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->db->fetchAll('rpt_unregistered_refs')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section`, `Area`, ABS(`Region`), 
                    FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee', 
                    'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 
                    'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 
                    'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`"
            )
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return Collection
     */
    public function getRefsConcussion($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->db->fetchAll('rpt_ref_cdc')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section` , `Area` , ABS(`Region`) , `Last Name` , `First Name` , `AYSOID`"
            )
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return Collection
     */
    public function getSafeHavenRefs($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->db->fetchAll('rpt_safehaven')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section` , `Area` , ABS(`Region`) , `Last Name` , `First Name` , `AYSOID`"
            )
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getCompositeRefCerts($userKey, $limit = self::BIGINT)
    {
        $results = $this->db->fetchAll('rpt_ref_certs')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section`, `Area`, ABS(`Region`), 
                    FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee', 
                    'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 
                    'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 
                    'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`"
            )
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return Collection
     */
    public function getRefsWithNoBSCerts($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->db->fetchAll('rpt_nocerts')
            ->where('sar', 'like', "%$userKey%")
            ->orWhere('area', '=', "")
            ->orderByRAW("`Section`, `Area`, ABS(`Region`), 
                    FIELD(`CertificationDesc`, 'National Referee','National 2 Referee', 'Advanced Referee', 
                    'Intermediate Referee', 'Regional Referee', 'Regional Referee & Safe Haven Referee', 
                    'Assistant Referee', 'Assistant Referee & Safe Haven Referee', 'U-8 Official', 
                    'U-8 Official & Safe Haven Referee', '') , `Last Name` , `First Name` , `AYSOID`"
            )
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getReports()
    {
        return $this->db->fetchAll('reports')
            ->where('show','=', 1)
            ->orderBy('seq')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getReportNotes()
    {
        return $this->db->fetchAll('report_notes')
            ->orderBy('seq')
            ->get();
    }


    /**
     * @return mixed
     */
    public function getUpdateTimestamp()
    {
        $ts = $this->db->fetchAll('rpt_lastUpdate')
            ->orderBy('timestamp', 'desc')
            ->limit(1)
            ->get();

        $updated = $this->getZero($ts)->timestamp;

        return $updated;
    }

//    public function getTableHeaders($tableName)
//    {
//        if (is_null($tableName)) {
//            return null;
//        }
//
//        $results = $this->db::schema()->getColumnListing($tableName);
//
//        return $results;
//    }

//Log writer

    /**
     * @param $key
     * @param $msg
     * @return null
     */
    public function logInfo($key, $msg)
    {
        $data = [
            'timestamp' => date('Y-m-d H:i:s'),
            'projectKey' => $key,
            'note' => $msg,
        ];

        $this->db->fetchAll('log')
            ->insert($data);

        return null;
    }

    /**
     * @return mixed[]
     */
    public function getAccessLog()
    {
        return $this->db->fetchAll("SELECT * FROM crs_log");
    }

    /**
     *
     */
//    public function showVariables()
//    {
//        return $this->db->getConnection();
//    }

//Log reader

    /**
     * @param $key
     * @param $userName
     * @return string|null
     * @throws \Exception
     */
//    public function getLastLogon($key, $userName)
//    {
//        $timestamp = null;
//
//        $ts = $this->db->fetchAll('log')
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
