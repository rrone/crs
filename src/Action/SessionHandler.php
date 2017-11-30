<?php

namespace App\Action;

use Illuminate\Database\Capsule\Manager;

/*
 * Session storage in database
 *
 * Needed on WPENGINE.COM because PHPSESSID is explicitly ignored for performance
 * Reference: https://wpengine.com/support/cookies-and-php-sessions/
 *
 * Storing Sessions in a Database
 * PHP Magazine on 14 Dec 2004
 * Reference: http://shiflett.org/articles/storing-sessions-in-a-database
 */

class SessionHandler
{
    private $sess_db;
    
    public function __construct(Manager $dbManager)
    {
        $this->sess_db = $dbManager;

    }

    function _open($sess_path, $sess_name)
    {
        return true;
    }

    function _close()
    {
        return true;
    }

    function _read($sess_id)
    {

//    $sess_id = $this->sess_db::raw($sess_id);

        $record = $this->sess_db->table('sessions')
            ->where('id', '=', $sess_id)
            ->get();

        if (isset($record[0])) {
            return $record[0]->data;
        }

        return '';
    }

    function _write($sess_id, $sess_data)
    {
        $access = time();

//    $sess_id = $this->sess_db::raw($sess_id);
//    $access = $this->sess_db::raw($access);
//    $sess_data = $this->sess_db::raw($sess_data);

        $result = $this->sess_db->table('sessions')
            ->where(['id' => $sess_id])
            ->get();

        if (empty($result[0])) {
            $this->sess_db->table('sessions')
                ->insert(
                    [
                        'id' => $sess_id,
                        'access' => $access,
                        'data' => $sess_data,
                    ]
                );
        } else {
            $this->sess_db->table('sessions')
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

    function _destroy($sess_id)
    {
//    $sess_id = $this->sess_db::raw($sess_id);

        $this->sess_db->table('sessions')
            ->where('id', '=', $sess_id)
            ->delete();

        return true;
    }

    function _clean($max)
    {
        $old = time() - $max;
//    $old = $this->sess_db::raw($old);
        $this->sess_db->table('sessions')
            ->where('access', '<', $old)
            ->delete();

        return true;
    }
}

