<?php

namespace App\Controller;

use App\Abstracts\AbstractController2;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;

class EndController extends AbstractController2
{
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);
    }

    /**
     * @throws \Exception
     */
    #[Route('/unk', name: 'unk')]
    #[Route('/end', name: 'end')]
    public function __index(Request $request): RedirectResponse
    {
        $this->logStamp($request);

        $session = $this->request->getSession();
        $session->invalidate();

        return $this->redirectToRoute('logon');
    }
}
