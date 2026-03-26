<?php

namespace App\Controller;

use App\Abstracts\AbstractController2;
use App\Services\ExportXl;
use Doctrine\DBAL\Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController2
{
    private ExportXl $exportXl;

    /**
     * ExportController constructor.
     *
     * @throws \Exception
     */
    public function __construct(RequestStack $requestStack, ExportXl $exportXl)
    {
        parent::__construct($requestStack);

        $this->exportXl = $exportXl;
    }

    /**
     *
     * @param Request $request
     * @return RedirectResponse|Response
     *
     * @throws Exception
     */
    #[Route("/crct", name: "crct")]
    #[Route("/ra", name: "ra")]
    #[Route("/ri", name: "ri")]
    #[Route("/rie", name: "rie")]
    #[Route("/ruc", name: "ruc")]
    #[Route("/urr", name: "urr")]
    #[Route("/nra", name: "nra")]
    #[Route("/rsh", name: "rsh")]
    #[Route("/rcdc", name: "rcdc")]
    #[Route("/rss", name: "rss")]
    #[Route("/rssx", name: "rssx")]
    #[Route("/rsca", name: "rsca")]
    #[Route("/rls", name: "rls")]
    #[Route("/rxr", name: "rxr")]
    #[Route("/newcert", name: "newcert")]
    #[Route("/xra", name: "xra")]
    #[Route("/xri", name: "xri")]
    #[Route("/xrie", name: "xrie")]
    #[Route("/xnra", name: "xnra")]
    public function invoke(Request $request): RedirectResponse|Response
    {
        if (!$this->isAuthorized()) {
            return $this->redirectToRoute('/');
        }
        $this->logStamp($request);
        $request->request->set('user', ((array) $this->user)['name']);
        $request->request->set('baseURL', $this->generateUrl('logon'));
        $request->request->set('admin', ((array) $this->user)['admin']);
        $request->request->set('section', ((array) $this->user)['section']);

        return $this->exportXl->invoke($request);
    }
}
