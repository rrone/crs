<?php

namespace App\Action\Reports;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractView;
use App\Action\DataWarehouse;

class ReportsView extends AbstractView
{
    private $games;
    private $description;

    public function __construct(Container $container, DataWarehouse $dataWarehouse)
    {
        parent::__construct($container, $dataWarehouse);

        $this->games = null;
        $this->description = 'No matches scheduled';
    }

    public function handler(Request $request, Response $response)
    {
        $this->user = $request->getAttribute('user');
    }

    public function render(Response &$response)
    {
        $content = array(
            'view' => array(
                'admin' => $this->user->admin,
                'header' => $this->renderHeader(),
                'notes' =>  $this->dw->getReportNotes(),
                'content' => $this->renderView(),
                'message' => null,
            ),
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

        $html .= "<h3 class=\"center\">Available reports for volunteers in Section 1:</h3>\n";

        $reports = $this->dw->getReports();

        foreach($reports as $report) {
            $href = $this->getBaseURL($report->key);
            var_dump($href);
            $html .= "<h3 class=\"center\"><a  href=$href download>$report->text</a> $report->notes</h3>\n";
            $html .= "<div class='clear-fix'></div>\n";
            $html .= "</h3>\n";
        }
die();
        $html .= "<hr>\n";
        $href = $this->getBaseURL('end');
        $html .= "<h3 class=\"center\"><a href=$href>Log Off</a></h3>\n";

        return $html;

    }
}
