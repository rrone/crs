<?php

namespace  App\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;@Route::class;

use App\Abstracts\AbstractController2;
use App\Services\ExportXl;

class ExportController extends AbstractController2
{
    private $exportXl;

	public function __construct(ExportXl $exportXl)
    {
		parent::__construct();

        $this->exportXl = $exportXl;

    }

    /**
     * @Route("/export", name="index")
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
        $request->query->set('baseURL', $this->generateUrl('logon'));

        $response = $this->exportXl->handler($request, $response);

        return $response;
		
    }
}
