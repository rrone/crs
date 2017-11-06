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
     * @param integer $limit
     * @return mixed
     */
    public function getHighestRefCerts($limit = self::BIGINT)
    {
        $results = $this->db->table('hrc_tmp')
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param integer $limit
     * @return mixed
     */
    public function getRefAssessors($limit = self::BIGINT)
    {
        $results = $this->db->table('ra_tmp')
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param integer $limit
     * @return mixed
     */
    public function getRefInstructors($limit = self::BIGINT)
    {
        $results = $this->db->table('ri_tmp')
            ->limit($limit)
            ->get();

        return $results;
    }

    /**
     * @param integer $limit
     * @return Collection
     */
    public function getRefInstructorEvaluators($limit = self::BIGINT)
    {
        $results = $this->db->table('rie_tmp')
            ->limit($limit)
            ->get();

        return $results;
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

}
