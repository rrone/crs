<?php

namespace App\Abstracts;

use DateTime;
use DateTimeZone;

use App\Repository\DataWarehouse;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

abstract class AbstractController2 extends AbstractController
{
    //database connection
    protected $conn;

    /* @var Request */
    protected $request;

    /* @var DataWarehouse */
    protected $dw;

    /* @var bool */
    protected $isTest;

    //shared variables
    protected $root;
    protected $event;
    protected $user;

    //view variables
    protected $page_title;
    protected $dates;
    protected $location;
    protected $msg;
    protected $msgStyle;
    protected $menu;
    protected $server;

    protected $uri;

    public function __construct(RequestStack $requestStack)
    {
        $this->root = __DIR__.'/../..';

        $this->dw = new DataWarehouse();

        $this->page_title = "Section 1: Certification Reporting System";

        $this->isTest = false;

        $this->request = $requestStack->getCurrentRequest();

    }

    private function isTest()
    {
        return $this->getParameter('settings.test');
    }

    protected function isAuthorized()
    {
        $session = $this->request->getSession();

        if (!$session->get('authed')) {
            return null;
        }

        if (is_null($session->get('user'))) {
            return null;
        }

        $this->user = $session->get('user');

        return true;
    }

    protected function logStamp(Request $request)
    {
        $session = $this->request->getSession();

        if (is_null($session->get('admin'))) {
            return null;
        }

        $_GET = $request->query;
        $uri = $request->getUri();
        $user = isset($this->user) ? $this->user->name : 'Anonymous';
        $post = $request->isMethod('post') ? 'with updated ref assignments' : '';

        switch ($uri) {
            case $this->generateUrl('logon'):
            case '/':
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

    protected function getUpdateTimestamp()
    {
        $utc = $this->dw->getUpdateTimestamp();

        $ts = new DateTime($utc, new DateTimeZone('UTC'));
        $ts->setTimezone(new DateTimeZone('America/Los_Angeles'));

        return $ts->format('Y-m-d H:i T');
    }

    protected function getBaseContent()
    {
        $server = $this->request->server->get('SERVER_NAME');
        $banner = str_replace('$server',$server,$this->getParameter('banner') );

        return array(
            'banner' => $banner,
            'root' => $this->generateUrl('logon'),
            'email' => $this->getParameter('sra')['email'],
            'issueTracker' => $this->getParameter('issueTracker'),
            'version' => $this->getParameter('app.version'),
            'updated' => $this->getUpdateTimestamp(),
        );

    }
}
