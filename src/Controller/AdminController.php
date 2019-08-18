<?php
namespace App\Controller;

use App\Abstracts\AbstractController2;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;@Route::class;

class AdminController extends AbstractController2
{
    /* @var AdminView */
    private $adminView;

    /**
     * AdminController constructor.
     * @param AdminView $adminView
     */
    public function __construct(AdminView $adminView) {

	    parent::__construct();
		
        $this->adminView = $adminView;

    }

    /**
     * @Route("/admin", name="admin")
     * @param Request $request
     * @param Response $response
     * @return RedirectResponse|null
     */
    public function index(Request $request, Response $response)
    {
        if(!$this->isAuthorized() || !$this->user->admin) {
            return $this->redirectToRoute('reports');
        };

        $this->logStamp($request);

        $request = $request->query->set('user', $this->user);

        $response->headers->set('admin', $this->generateUrl('admin'));
        $result = $this->adminView->handler($request, $response);

        switch ($result) {
             case 'Done':

                 return $this->redirectToRoute('reports');

            case 'ExportLog':

                return $this->redirectToRoute('logExport');
        }

        return $this->adminView->render($response);

    }
}
