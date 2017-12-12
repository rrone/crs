<?php

namespace App\Action;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Collection;
use DateTime;
use DateTimeZone;

/**
 * Class DataWarehouse
 * @package App\Action
 */
class DataWarehouse
{
    /* @var Manager */
    private $db;

    /**
     * @const BIGINT
     */
    const BIGINT = 9223372036854775807;

    /**
     * DataWarehouse constructor.
     * @param Manager $db
     */
    public function __construct(Manager $db)
    {
        $this->db = $db;
    }

    /**
     * @param $elem
     * @return null|object
     */
    private function getZero($elem)
    {
        return isset($elem[0]) ? (object)$elem[0] : null;
    }

    //User table functions

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAllUsers()
    {
        return $this->db->table('users')
            ->get();
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

        $user = $this->db->table('users')
            ->where('name', 'like', $name)
            ->get();

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

        $user = $this->db->table('users')
            ->where('hash', 'like', $hash)
            ->get();

        return $this->getZero($user);

    }

    /**
     * @param $user
     * @return null
     */
    public function setUser($user)
    {
        if (empty($user)) {
            return null;
        }

        $u = $this->getUserByName($user['name']);
        if (empty($u)) {
            $newUser = array(
                'name' => $user['name'],
                'enabled' => $user['enabled'],
                'hash' => $user['hash'],
            );

            $this->db->table('users')
                ->insert([$newUser]);

            $newUser = $this->getUserByName($newUser['name']);

            return $newUser->id;

        } else {
            $hash = $user['hash'];

            $this->db->table('users')
                ->where('id', $u->id)
                ->update(
                    [
                        'hash' => $hash,
                    ]
                );

            return $u->id;
        }

    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return mixed
     */
    public function getHighestRefCerts($userKey, $limit = self::BIGINT)
    {
        $results = $this->db->table('tmp_hrc')
            ->where('sar', 'like', "%$userKey%")
            ->limit($limit)
            ->get();

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

        $results = $this->db->table('tmp_ra')
            ->where('sar', 'like', "%$userKey%")
            ->limit($limit)
            ->get();

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

        $results = $this->db->table('tmp_nra')
            ->where('sar', 'like', "%$userKey%")
            ->limit($limit)
            ->get();

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

        $results = $this->db->table('tmp_ri')
            ->where('sar', 'like', "%$userKey%")
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

        $results = $this->db->table('tmp_rie')
            ->where('sar', 'like', "%$userKey%")
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

        $results = $this->db->table('tmp_nocerts')
            ->where('sar', 'like', "%$userKey%")
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param mixed $userKey
     * @param integer $limit
     * @return Collection
     */
    public function getRefUpgrades($userKey = null, $limit = self::BIGINT)
    {
        if (is_null($userKey)) {
            $userKey = '%%';
        } else {
            $userKey = "%$userKey%";
        }

        $results = $this->db->table('tmp_ref_upgrades')
            ->where('sar', 'like', "%$userKey%")
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

        $results = $this->db->table('tmp_unregistered_refs')
            ->where('sar', 'like', "%$userKey%")
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

        $results = $this->db->table('tmp_safehaven')
            ->where('sar', 'like', "%$userKey%")
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

        $results = $this->db->table('tmp_ref_cdc')
            ->where('sar', 'like', "%$userKey%")
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getReports()
    {
        return $this->db->table('reports')
            ->orderBy('seq')
            ->get();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getReportNotes()
    {
        return $this->db->table('report_notes')
            ->orderBy('seq')
            ->get();
    }


    /**
     * @return mixed
     */
    public function getUpdateTimestamp()
    {
        $ts = $this->db->table('lastUpdate')
            ->orderBy('timestamp', 'desc')
            ->limit(1)
            ->get();

        $updated = $this->getZero($ts)->timestamp;

        return $updated;
    }

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

        $this->db->table('log')
            ->insert($data);

        return null;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getAccessLog()
    {
        return $this->db->table('log')
            ->get();
    }

    /**
     *
     */
    public function showVariables()
    {
        return $this->db->getConnection();
    }

//Log reader

    /**
     * @param $key
     * @param $userName
     * @return null|string
     */
    public function getLastLogon($key, $userName)
    {
        $timestamp = null;

        $ts = $this->db->table('log')
            ->where(
                [
                    ['projectKey', 'like', $key],
                    ['note', 'like', "$userName: CRS logon%"],
                ]
            )
            ->orderBy('timestamp', 'desc')
            ->limit(1)
            ->get();

        $ts = $this->getZero($ts);

        if (!empty($ts)) {
            $utc = new DateTime($ts->timestamp, new DateTimeZone('UTC'));
            $time = $utc->setTimezone(new DateTimeZone('America/Los_Angeles'));
            $timestamp = $time->format('Y-M-j H:i');
        }

        return $timestamp;
    }

//Session functions

    /**
     * @param $sess_id
     * @return string
     */
    function sessionRead()
    {
        $sess_id = $GLOBALS['_COOKIE'][ini_get('session.name')];

//    $sess_id = $this->db::raw($sess_id);

        $record = $this->db->table('sessions')
            ->where('id', '=', $sess_id)
            ->get();

        if (isset($record[0])) {

            session_decode($record[0]->data);

            return true;
        }

        return '';
    }

    function sessionWrite()
    {
        $sess_id = $GLOBALS['_COOKIE'][ini_get('session.name')];

        $access = time();

//    $sess_id = $this->sess_db::raw($sess_id);
//    $access = $this->sess_db::raw($access);
//    $sess_data = $this->sess_db::raw($sess_data);

        $sess_data = session_encode();

        $result = $this->db->table('sessions')
            ->where(['id' => $sess_id])
            ->get();

        if (empty($result[0])) {
            $this->db->table('sessions')
                ->insert(
                    [
                        'id' => $sess_id,
                        'access' => $access,
                        'data' => $sess_data,
                    ]
                );
        } else {
            $this->db->table('sessions')
                ->where(['id' => $sess_id])
                ->update(
                    [
                        'access' => $access,
                        'data' => $sess_data,
                    ]
                );
        }

        return true;
    }

}
