<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;@Route::class;

use App\Abstracts\AbstractController2;

class ReportsController extends AbstractController2
{
    /* @var ReportsView */
    private $reportsView;

    /**
     * ReportsController constructor.
     * @param ReportsView $reportsView
     */
    public function __construct(ReportsView $reportsView)
    {
        parent::__construct();

        $this->reportsView = $reportsView;

    }

    /**
     * @Route("/reports", name="index")
     * @param Request $request
     * @param Response $response
     * @return RedirectResponse|Response
     */
    public function index(Request $request, Response $response)
    {

        if(!$this->isAuthorized()) {
            return $this->redirectToRoute('logon');
        };

        $this->logStamp($request);

        $request->query->set('user', $this->user);

        $this->reportsView->handler($request, $response);
        $this->reportsView->render($response);

        return $response;
    }
}


