<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Abstracts\AbstractController2;

use Symfony\Component\Routing\Annotation\Route;@Route::class;

class EndController extends AbstractController2
{
    /**
     * @Route("/end", name="index")
     * @param Request $request
     * @return RedirectResponse
     */
    public function __index(Request $request)
    {
        $this->isAuthorized();

        $this->logStamp($request);

        $resp = $this->redirectToRoute($this->generateUrl('logon'));

        if(session_status() == PHP_SESSION_ACTIVE){
            session_unset();
            session_destroy();
        }

        return $resp;
    }
}


