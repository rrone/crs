<?php

namespace App\Abstracts;

use App\Repository\DataWarehouse;
use DateMalformedStringException;
use DateTime;
use DateTimeZone;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use stdClass as stdClass;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class AbstractController2 extends AbstractController
{
    protected Connection $conn;

    protected Request $request;

    protected DataWarehouse $dw;

    /**
     * @var SessionInterface
     */
    protected SessionInterface $session;

    // shared variables
    /**
     * @var stdClass
     */
    protected stdClass $user;

    // view variables
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
     * AbstractController2 constructor.
     *
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

        $this->page_title = 'Section 1: Certification Reporting System';
        $this->msg = [0 => '', 'add' => '', 'update' => ''];
        $this->msgStyle = [0 => '', 'add' => '', 'update' => ''];
    }

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
     *
     * @throws \Exception
     */
    protected function logStamp(Request $request): null
    {
        if ('Super Admin' == $this->session->get('name')) {
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
     * @throws Exception|DateMalformedStringException
     */
    protected function getUpdateTimestamp(): string
    {
        $utc = $this->dw->getUpdateTimestamp();

        $ts = new DateTime($utc, new DateTimeZone('UTC'));
        $ts->setTimezone(new DateTimeZone('America/Los_Angeles'));

        return $ts->format('Y-m-d H:i T');
    }

    /**
     * @throws Exception|DateMalformedStringException
     */
    protected function getBaseContent(): array
    {
        return [
            'root' => $this->generateUrl('logon'),
            'email' => $this->getParameter('sra')['email'],
            'issueTracker' => $this->getParameter('issueTracker'),
            'version' => $this->getParameter('app.version'),
            'updated' => $this->getUpdateTimestamp(),
        ];
    }
}
