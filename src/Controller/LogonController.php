<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Abstracts\AbstractController2;

use Symfony\Component\Routing\Annotation\Route;@Route::class;

class LogonController extends AbstractController2
{
    /* @var LogonView */
    private $logonView;

    /**
     * LogonController constructor.
     * @param LogonView $logonView
     */
    public function __construct(LogonView $logonView)
    {
        parent::__construct();

        $this->logonView = $logonView;
    }

    /**
     * @Route("/logon", name="index")
     * @param Request $request
     * @param Response $response
     * @return RedirectResponse|Response
     */
    public function index(Request $request, Response $response)
    {

        $this->logonView->handler($request, $response);

        if ($this->isAuthorized()) {
            $this->logStamp($request);

            return $this->redirectToRoute('reports');
        }

        $response = $this->logonView->render($response);

        return $response;
    }
}
