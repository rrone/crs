<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\Routing\Annotation\Route;
@Route::class;

use App\Abstracts\AbstractController2;

class ReportsController extends AbstractController2
{
    private bool $super;
    /**
     * ReportsController constructor.
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        parent::__construct($requestStack);

    }

    /**
     * @Route("/reports", name="reports")
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function index(Request $request)
    {
        if(!$this->isAuthorized()) {
            return $this->redirectToRoute('logon');
        };

        $this->logStamp($request);
        $session = $this->request->getSession();
        $this->user = $session->get('user');
        $this->super = $session->get('superadmin');

        return $this->renderPage();

    }

    public function renderPage()
    {
        $content = array(
            'admin' => $this->user->admin,
            'user' => $this->user->name,
            'notes' =>  $this->dw->getReportNotes(),
            'content' => $this->renderContent(),
            'message' => null
        );

        $content = array_merge($content, $this->getBaseContent());

        return $this->render('reports.html.twig', $content);
    }

    protected function renderContent()
    {
        $html = null;

        $u = $this->user->name;
        $html .= "<div id=\"reports\">";
        $html .= "<h3>Available reports for $u:</h3>\n";
        $html .= "<ul class=\"indent\">\n";

        $reports = $this->dw->getReports();
        foreach($reports as $report) {
            $report = (object) $report;
            if(!$report->admin || ($report->admin && $this->user->admin)) {
                try { // handle possible exception if database table report differs from defined routes
                    $href = $this->generateUrl($report->key);
                    $notes = empty($report->notes) ? null : "<span style='font-weight:normal'> ($report->notes)</span>";

                    $html .= "<li><h3><a  href=\"$href\" class=\"reportDownload\" >$report->text</a>$notes</h3></li>\n";
                } catch (\Exception $e) {
                }
            }
        }

        $html .= "</ul>\n";
        $html .= "<hr>\n";
        $hrefAdmin = $this->generateUrl('admin');
        $hrefEnd = $this->generateUrl('end');

        $html .= "<h3 class=\"center\">";
        if($this->super) {
            $html .= "<a href=$hrefAdmin >Admin Functions - </a> ";
        }
        $html .= "<a href=$hrefEnd >Log Off</a></h3>\n";

        return $html;

    }
}


