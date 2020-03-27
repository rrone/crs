<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

@Route::class;

use App\Services\LogExport;
use App\Abstracts\AbstractController2;

class LogExportController extends AbstractController2
{
    private $exporter;

    /**
     * LogExportController constructor
     * @param LogExport $logExport
     * @param RequestStack $requestStack
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
