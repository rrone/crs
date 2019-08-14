<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Abstracts\AbstractView;
use App\Repository\DataWarehouse;

class ReportsView extends AbstractView
{
    public function __construct(DataWarehouse $dataWarehouse)
    {
        parent::__construct($dataWarehouse);

    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->query->get('user');
    }

    public function render(Response &$response)
    {
        $content = array(
                'admin' => $this->user->admin,
                'user' => $this->user->name,
                'header' => $this->renderHeader(),
                'notes' =>  $this->dw->getReportNotes(),
                'content' => $this->renderView(),
                'message' => null,
                'updated' => $this->getUpdateTimestamp(),
        );

        $this->view->render($response, 'reports.html.twig', $content);
    }

    protected function renderHeader()
    {
        $html = null;

//        $uname = $this->user->name;

//        $html .= "<h3 class=\"center\">Welcome, $uname</h3>\n";

        if ($this->container['settings']['debug']) {
            $html .= <<<EOD
<div class="center">
    <h1>Section 1: Certification Reporting System</h1>
</div>
EOD;
        }

        return $html;
    }

    protected function renderView()
    {
        $html = null;

        $u = $this->user->name;
        $html .= "<div id=\"reports\">";
        $html .= "<h3>Available reports for $u:</h3>\n";
        $html .= "<ul class=\"indent\">\n";

        $reports = $this->dw->getReports();

        foreach($reports as $report) {
            if(!$report->admin || ($report->admin && $this->user->admin)) {
                try { // handle possible exception if database table report differs from defined routes
                    $href = $this->getBaseURL($report->key);
                    $notes = empty($report->notes) ? null : "<span style='font-weight:normal'> ($report->notes)</span>";

                    $html .= "<li><h3><a  href=\"$href\" class=\"reportDownload\" >$report->text</a>$notes</h3></li>\n";
                } catch (\Exception $e) {

                }
            }
        }

        $html .= "</ul>\n";
        $html .= "<hr>\n";
        $href = $this->getBaseURL('end');
        $html .= "<h3 class=\"center\"><a href=$href >Log Off</a></h3>\n";

        return $html;

    }
}
