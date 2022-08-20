<?php

namespace App\Controller;

use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;

use App\Abstracts\AbstractController2;
use App\Services\ExportXl;

class ExportController extends AbstractController2
{
    private ExportXl $exportXl;

    /**
     * ExportController constructor.
     * @param RequestStack $requestStack
     * @param ExportXl $exportXl
     * @throws \Exception
     */
    public function __construct(RequestStack $requestStack, ExportXl $exportXl)
    {
        parent::__construct($requestStack);

        $this->exportXl = $exportXl;

    }

    /**
     * @Route("/crct", name="crct")
     * @Route("/ra", name="ra")
     * @Route("/ri", name="ri")
     * @Route("/rie", name="rie")
     * @Route("/ruc", name="ruc")
     * @Route("/urr", name="urr")
     * @Route("/nra", name="nra")
     * @Route("/rsh", name="rsh")
     * @Route("/rcdc", name="rcdc")
     * @Route("/rss", name="rss")
     * @Route("/rsca", name="rsca")
     * @Route("/rls", name="rls")
     * @Route("/rxr", name="rxr")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws Exception
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

}
