<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

use App\Services\LogExport;
use App\Abstracts\AbstractController2;

class LogExportController extends AbstractController2
{
    private LogExport $exporter;

    /**
     * LogExportController constructor
     * @param LogExport $logExport
     * @param RequestStack $requestStack
     * @throws \Exception
     */
    public function __construct(LogExport $logExport, RequestStack $requestStack)
    {
        parent::__construct($requestStack);

        $this->exporter = $logExport;
    }

    /**
     * @Route("/log", name="log")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
     */
    public function index(Request $request)
    {
        if (!$this->isAuthorized()) {
            return $this->redirectToRoute('/');
        }

        if (!$this->user->admin) {
            return $this->redirectToRoute('reports');
        }

        $this->logStamp($request);

        return $this->exporter->handler();
    }

}
