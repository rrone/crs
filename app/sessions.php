<?php
/*
 * Session storage in database
 *
 * Needed on WPENGINE.COM because PHPSESSID is explicitly ignored for performance
 * Reference: https://wpengine.com/support/cookies-and-php-crs_sessions/
 *
 * Storing crs_sessions in a Database
 * PHP Magazine on 14 Dec 2004
 * Reference: http://shiflett.org/articles/storing-crs_sessions-in-a-database
 */

$config = include '../config/config.php';

$sess_db = new mysqli(
    $config['wpe']['host'],
    $config['wpe']['username'],
    $config['wpe']['password'],
    $config['wpe']['database']
);

class CRSSessionHandler implements SessionHandlerInterface
{

//    public function open($savePath, $sessionName)
//    {
//        global $config;
//        global $sess_db;
//
//        if ($sess_db = mysqli_connect($config['wpe']['host'], $config['wpe']['username'], $config['wpe']['password'])) {
//            return mysqli_select_db($sess_db, $config['wpe']['database']);
//        }
//
//        return false;
//    }
//
//    public function close()
//    {
//        global $sess_db;
//
//        return $sess_db->close();
//    }
//
//    public function read($id)
//    {
//        global $sess_db;
//
////    $id = $sess_db->real_escape_string($id);
//        $stmt = $sess_db->prepare("SELECT data FROM `crs_sessions` WHERE  id = '$id'");
//        $stmt->bind_params($id, $access, $data);
//        $stmt->execute();
//
//        if ($result = $sess_db->query($sql)) {
//            if (mysqli_num_rows($result)) {
//                $record = mysqli_fetch_assoc($result);
//
//                return $record['data'];
//            }
//        }
//
//        return '';
//    }
//
//    public function write($id, $data)
//    {
//        global $sess_db;
//
//        $access = time();
//
////    $id = $sess_db->real_escape_string($id);
////    $access = $sess_db->real_escape_string($access);
////    $data = $sess_db->real_escape_string($data);
//
//        $stmt = $sess_db->prepare("REPLACE INTO `crs_sessions` VALUES  ('?', '?', '?')");
//        $stmt->bind_params($id, $access, $data);
//        $stmt->execute();
//        $stmt->close();
//
//        return true;
//    }
//
//    public function destroy($id)
//    {
//        global $sess_db;
//
////    $id = $sess_db->real_escape_string($id);
//        $stmt = $sess_db->prepare("DELETE FROM `crs_sessions` WHERE  id = '?'");
//        $stmt->bind_params($id);
//        $stmt->execute();
//        $stmt->close();
//
//        return $sess_db->query($sql);
//    }
//
//    public function gc($maxlifetime)
//    {
//        global $sess_db;
//
//        $old = time() - $maxlifetime;
////    $old = $sess_db->real_escape_string($old);
//        $sql = "DELETE FROM crs_sessions WHERE access < '$old'";
//
//        return $sess_db->query($sql);
//    }

    private $savePath;

    public function open($savePath, $sessionName)
    {
        $savePath = __DIR__ . '/sessions';

        $this->savePath = $savePath;
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    public function write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    public function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }
}

$handler = new CRSSessionHandler();

session_set_save_handler($handler, true);

session_name('CRSID');

session_start();

