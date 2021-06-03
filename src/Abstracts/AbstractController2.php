<?php

namespace App\Abstracts;

use DateTime;
use DateTimeZone;

use App\Repository\DataWarehouse;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
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
    protected $session;

    //shared variables
    /**
     * @var mixed|stdClass
     */
    protected $user;

    //view variables
    /**
     * @var string
     */
    protected string $page_title;
    /**
     * @var array|string[]
     */
    protected array $msg;
    /**
     * @var array|string[]
     */
    protected array $msgStyle;

    /**
     * AbstractController2 constructor
     * @param RequestStack $requestStack
     * @throws \Exception
     */
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

    /**
     * @return bool|null
     */
    protected function isAuthorized(): ?bool
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

    /**
     * @param Request $request
     * @return null
     * @throws \Exception
     */
    protected function logStamp(Request $request)
    {
        if ($this->session->get('name') == 'Super Admin') {
            return null;
        }

        $_GET = $request->query;
        $uri = $request->getRequestUri();
        $user = $this->user->name ?? 'Anonymous';
        $post = $request->isMethod('post') ? 'with updated ref assignments' : '';

        switch ($uri) {
            case '/':
            case '/logon':
                $logMsg = $uri != $this->generateUrl('admin') ? "$user: CRS logon" : null;
                break;
            case '/end':
                $logMsg = "$user: CRS log off";
                break;
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

    /**
     * @return string
     * @throws Exception
     */
    protected function getUpdateTimestamp(): string
    {
        $utc = $this->dw->getUpdateTimestamp();

        $ts = new DateTime($utc, new DateTimeZone('UTC'));
        $ts->setTimezone(new DateTimeZone('America/Los_Angeles'));

        return $ts->format('Y-m-d H:i T');
    }

    /**
     * @return array
     * @throws Exception
     */
    protected function getBaseContent(): array
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
