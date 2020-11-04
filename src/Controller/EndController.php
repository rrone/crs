<?php

namespace App\Controller;

use App\Abstracts\AbstractController2;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;

@Route::class;

class EndController extends AbstractController2
{
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);

    }

    /**
     * @Route("/end", name="end")
     * @Route("/unk", name="unk")
     * @param Request $request
     * @return RedirectResponse
     */
    public function __index(Request $request)
    {
        $this->logStamp($request);

        $session = $this->request->getSession();
        $session->invalidate();

        return $this->redirectToRoute('logon');
    }
}


