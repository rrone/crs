<?php

namespace App\Action;

use Slim\Container;
use Slim\Http\Request;

abstract class AbstractController
{
    //database connection
    protected $conn;

    /* @var Container */
    protected $container;

    //shared variables
    protected $root;

    //session variables
    protected $event;
    protected $user;
    protected $authed;

    public function __construct(Container $container)
    {
        $this->container = $container;

        $this->root = __DIR__ . '/../../var';
    }

    private function isTest()
    {
        return $this->container->get('settings.test');
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

        $_GET = $request->getParams();
        $uri = $request->getUri()->getPath();
        $uriPath = $uri == '/' ? 'logon' : str_replace('/', '', $uri);
        $user = isset($this->user) ? $this->user->name : 'Anonymous';
        $post = $request->isPost() ? 'with updated ref assignments' : '';

        switch ($uri) {
            case $this->getBaseURL('logonPath'):
            case '/':
            case '/logon':
                //TODO: Why is $uri == '/adm' passing this case?
                $logMsg = $uri != $this->getBaseURL('adminPath') ? "$user: Scheduler logon" : null;
                break;
            case $this->getBaseURL('endPath'):
            case '/end':
                $logMsg = "$user: CRS log off";
                break;
            case $this->getBaseURL('reportsPath'):
            case '/reports':
                if (!empty($post)) {
                    $logMsg = "$user: CRS $uriPath dispatched $post";
                } else {
                    return null;
                }
                break;
            default:
                $logMsg = "$user: CRS $uriPath dispatched";
                break;
        }

        if (!is_null($logMsg)) {
            $dw->logInfo($projectKey, $logMsg);
        }

        return null;

    }

    protected function getBaseURL($path)
    {
        $request = $this->container->get('request');

        $baseUri = $request->getUri()->getBasePath() . $this->container->get($path);

        return $baseUri;
    }

}
