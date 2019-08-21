<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;@Route::class;

use App\Services\LogExport;

use App\Abstracts\AbstractController2;

class LogExportController extends AbstractController2
{
    private $exporter;

    /**
     * LogExportController constructor.
     * @param LogExport $logExport
     */
    public function __construct(LogExport $logExport)
    {
        parent::__construct();

        $this->exporter = $logExport;
    }

    /**
     * @Route("/log", name="log")
     * @param Request $request
     * @param Response $response
     * @return RedirectResponse|Response
     */
    public function index(Request $request, Response $response)
    {
        if(!$this->isAuthorized()) {
            return $this->redirectToRoute('/');
        };

        if (!$this->user->admin) {
            return $this->redirectToRoute('reports');
        }

        $this->logStamp($request);

        $request->query->set('user', $this->user);

        $response = $this->exporter->handler($request, $response);

        return $response;
    }
}
