<?php

namespace App\Abstracts;

use App\Repository\DataWarehouse;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractController2 extends AbstractController
{
    //database connection
    protected $conn;

    /* @var DataWarehouse */
    protected $dw;

    /* @var bool */
    protected $isTest;

    //shared variables
    protected $root;

    //session variables
    protected $event;
    protected $user;
    protected $authed;

    public function __construct($url)
    {
        $this->root = __DIR__.'/../..';
        $config = new Configuration();

        $connParams = array('url' => $url);
        $conn = DriverManager::getConnection($connParams, $config);
        $this->dw = new DataWarehouse($conn);

        $this->isTest = false;
    }

    private function isTest()
    {
        return $this->getParameter('settings.test');
    }

    protected function isAuthorized()
    {
        if ($this->isTest() && isset($this->container['session'])) {
            unset ($_SESSION);
            $session = $this->container['session'];
            $_SESSION['authed'] = $session['authed'];
            $_SESSION['user'] = $session['user'];
            if (isset($session['game_id'])) {
                $_SESSION['game_id'] = $session['game_id'];
            }
        }

        $this->authed = isset($_SESSION['authed']) ? $_SESSION['authed'] : null;
        if (!$this->authed) {
            return null;
        }

        $this->user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

        if (is_null($this->user)) {
            return null;
        }

        return true;
    }

    protected function logStamp(Request $request)
    {
        if (isset($_SESSION['admin'])) {
            return null;
        }

        $dw = $this->container['dw'];

        if (is_null($dw)) {
            return null;
        }

        $_GET = $request->query;
        $uri = $request->getUri();
        $user = isset($this->user) ? $this->user->name : 'Anonymous';
        $post = $request->isMethod('post') ? 'with updated ref assignments' : '';

        switch ($uri) {
            case $this->generateUrl('logon'):
            case '/':
            case '/logon':
                //TODO: Why is $uri == '/adm' passing this case?
                $logMsg = $uri != $this->generateUrl('admin') ? "$user: CRS logon" : null;
                break;
            case $this->generateUrl('end'):
            case '/end':
                $logMsg = "$user: CRS log off";
                break;
            case $this->generateUrl('reports'):
            case '/reports':
                if (!empty($post)) {
                    $logMsg = "$user: CRS $uri dispatched $post";
                } else {
                    return null;
                }
                break;
            default:
                $logMsg = "$user: CRS $uri dispatched";
                break;
        }

        if (!is_null($logMsg)) {
            $this->dw->logInfo('CRS', $logMsg);
        }

        return null;

    }

}
