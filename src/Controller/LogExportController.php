<?php

namespace App\Controller;

use App\Abstracts\AbstractController2;
use App\Services\LogExport;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LogExportController extends AbstractController2
{
    private LogExport $exporter;

    /**
     * LogExportController constructor.
     *
     * @throws \Exception
     */
    public function __construct(LogExport $logExport, RequestStack $requestStack)
    {
        parent::__construct($requestStack);

        $this->exporter = $logExport;
    }

    /**
     * @throws Exception
     */
    #[Route('/log', name: 'log')]
    public function index(Request $request): RedirectResponse|Response
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
