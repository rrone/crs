<?php
namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Abstracts\AbstractController2;

use Symfony\Component\Routing\Annotation\Route;@Route::class;

class LogonController extends AbstractController2
{
    /* @var LogonView */
    private $logonView;

    /* @var RequestStack */
    private $requestStack;

    /**
     * LogonController constructor.
     * @param LogonView $logonView
     * @param RequestStack $requestStack
     * @param $conn
     */
    public function __construct(LogonView $logonView, RequestStack $requestStack, $url)
    {
        parent::__construct($url);

        $this->logonView = $logonView;
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/", name="home")
     * @Route("/logon", name="logon")
     * @return RedirectResponse|Response
     */
    public function index()
    {
        $request = $this->requestStack->getCurrentRequest();
        $request->query->set('url', $this->generateUrl('logon'));
        $response = new Response();

        $this->logonView->handler($request);

        if ($this->isAuthorized()) {
            $this->logStamp($request);

            return $this->redirectToRoute('reports');
        }

        $response = $this->render('logon.html.twig', $this->logonView->render());

        return $response;
    }
}
