<?php
namespace App\Action;

use Illuminate\Database\Capsule\Manager;
use Illuminate\Support\Collection;
use DateTime;
use DateTimeZone;

/**
 * Class SchedulerRepository
 * @package App\Action
 */
class DataWarehouse
{
    /* @var Manager */
    private $db;

    /**
     * SchedulerRepository constructor.
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
                ->update([
                    'hash' => $hash,
                ]);

            return $u->id;
        }

    }

    /**
     * @return mixed
     */
    public function getHighestRefCerts()
    {
        $result = $this->db->get('GetHighestCertification()');
        
        return $result;
    }
}
