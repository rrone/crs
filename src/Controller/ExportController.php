<?php

namespace App\Controller;

use stdClass;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;

@Route::class;

use App\Abstracts\AbstractController2;
use App\Services\ExportXl;

class ExportController extends AbstractController2
{
    private ExportXl $exportXl;

    /**
     * ExportController constructor.
     * @param RequestStack $requestStack
     * @param ExportXl $exportXl
     */
    public function __construct(RequestStack $requestStack, ExportXl $exportXl)
    {
        parent::__construct($requestStack);

        $this->exportXl = $exportXl;

    }

    /**
     * @Route("/bshca", name="bshca")
     * @Route("/ra", name="ra")
     * @Route("/ri", name="ri")
     * @Route("/rie", name="rie")
     * @Route("/ruc", name="ruc")
     * @Route("/urr", name="urr")
     * @Route("/nra", name="nra")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function invoke(Request $request)
    {
        if (!$this->isAuthorized()) {
            return $this->redirectToRoute('/');
        }

        $this->logStamp($request);

        $request->request->set('user', $this->user);
        $request->request->set('baseURL', $this->generateUrl('logon'));

        return $this->exportXl->invoke($request);

    }

    /**
     * @Route("/hrc", name="hrc")
     * @Route("/nocerts", name="nocerts")
     * @Route("/rcdc", name="rcdc")
     * @Route("/rsh", name="rsh")
     * @Route("/xxx", name="xxx")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    // provided for testing unused code in ExportXl
    public function index(Request $request)
    {
        $user = (object) [
            'name' => 'test',
            'enabled' => '0',
            'admin' => false,
            'section' => 1,
        ];

        $request->request->set('user', $user);
        $request->request->set('baseURL', $this->generateUrl('logon'));

        return $this->redirectToRoute('reports');

    }

}
