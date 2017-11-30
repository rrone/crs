<?php

namespace App\Action;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use Dflydev\FigCookies\FigRequestCookies;

abstract class AbstractController
{
    //database connection
    protected $conn;

    /* @var Container */
    protected $container;

    protected $crsID;
    protected $SESSION;

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

    public function __invoke(Request $request, Response $response, $args)
    {
        $this->crsID = FigRequestCookies::get($request, 'CRSID');

    }

    private function isTest()
    {
        return $this->container->get('settings.test');
    }

    protected function isAuthorized()
    {
        if ($this->isTest() && isset($this->container['session'])) {
            unset ($this->SESSION);
            $session = $this->container['session'];
            $this->SESSION['authed'] = $session['authed'];
            $this->SESSION['user'] = $session['user'];
            if (isset($session['game_id'])) {
                $this->SESSION['game_id'] = $session['game_id'];
            }
        }

var_dump($this->SESSION);
        $this->authed = isset($this->SESSION['authed']) ? $this->SESSION['authed'] : null;
        if (!$this->authed) {
            return null;
        }

        $this->user = isset($this->SESSION['user']) ? $this->SESSION['user'] : null;

        if (is_null($this->user)) {
            return null;
        }

        return true;
    }

    protected function logStamp(Request $request)
    {
        if (isset($this->SESSION['admin'])) {
            return null;
        }

        $dw = $this->container['dw'];

        if (is_null($dw)) {
            return null;
        }

        $_GET = $request->getParams();
        $uri = $request->getUri()->getPath();
        $user = isset($this->user) ? $this->user->name : 'Anonymous';
        $post = $request->isPost() ? 'with updated ref assignments' : '';

        switch ($uri) {
            case $this->getBaseURL('logon'):
            case '/':
            case '/logon':
                //TODO: Why is $uri == '/adm' passing this case?
                $logMsg = $uri != $this->getBaseURL('admin') ? "$user: CRS logon" : null;
                break;
            case $this->getBaseURL('end'):
            case '/end':
                $logMsg = "$user: CRS log off";
                break;
            case $this->getBaseURL('reports'):
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
            $dw->logInfo('CRS', $logMsg);
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
