<?php

namespace App\Abstracts;

use DateTime;
use DateTimeZone;

use App\Repository\DataWarehouse;

use Doctrine\DBAL\Connection;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class AbstractController2 extends AbstractController
{
    /** @var Connection */
    protected Connection $conn;

    /** @var Request */
    protected Request $request;

    /** @var DataWarehouse */
    protected DataWarehouse $dw;

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    //shared variables
    protected stdClass $user;

    //view variables
    protected string $page_title;
    protected array $msg;
    protected array $msgStyle;

    public function __construct(RequestStack $requestStack)
    {
        global $kernel;

        $conn = $kernel->getContainer()->get('doctrine.dbal.default_connection');
        $this->dw = new DataWarehouse($conn);

        $r = $requestStack->getCurrentRequest();
        if (!is_null($r)) {
            $this->request = $r;
            $this->session = $this->request->getSession();
            $u = $this->session->get('user');
            $this->user = is_null($u) ? new stdClass() : $u;
        }

        $this->page_title = "Section 1: Certification Reporting System";
        $this->msg = [0 => '', 'add' => '', 'update' => ''];
        $this->msgStyle = [0 => '', 'add' => '', 'update' => ''];

    }

    protected function isAuthorized()
    {
        if (!$this->session->get('authed')) {
            return null;
        }

        if (is_null($this->session->get('user'))) {
            return null;
        }

        $this->user = $this->session->get('user');

        return true;
    }

    protected function logStamp(Request $request)
    {
        if (is_null($this->session->get('admin'))) {
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
        return array(
            'root' => $this->generateUrl('logon'),
            'email' => $this->getParameter('sra')['email'],
            'issueTracker' => $this->getParameter('issueTracker'),
            'version' => $this->getParameter('app.version'),
            'updated' => $this->getUpdateTimestamp(),
        );
    }
}
