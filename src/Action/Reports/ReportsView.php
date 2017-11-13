<?php

namespace App\Action\Reports;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractView;
use App\Action\DataWarehouse;
use DateTime;
use DateTimeZone;

class ReportsView extends AbstractView
{
    public function __construct(Container $container, DataWarehouse $dataWarehouse)
    {
        parent::__construct($container, $dataWarehouse);

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
                'user' => $this->user->name,
                'header' => $this->renderHeader(),
                'notes' =>  $this->dw->getReportNotes(),
                'content' => $this->renderView(),
                'message' => null,
                'updated' => $this->getUpdateTimestamp(),
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
            if(!$report->admin || ($report->admin && $this->user->admin)) {
                $href = $this->getBaseURL($report->key);

                $html .= "<h3 class=\"center\"><a  href=$href download>$report->text</a><span style='font-weight:normal'>$report->notes</span></h3>\n";
                $html .= "<div class='clear-fix'></div>\n";
                $html .= "</h3>\n";
            }
        }

        $html .= "<hr>\n";
        $href = $this->getBaseURL('end');
        $html .= "<h3 class=\"center\"><a href=$href>Log Off</a></h3>\n";

        return $html;

    }

    protected function getUpdateTimestamp()
    {
        $utc = $this->dw->getUpdateTimestamp();

        $ts = new DateTime($utc, new DateTimeZone('UTC'));
        $ts->setTimezone(new DateTimeZone('PST'));

        return $ts->format('Y-m-d H:i:s');;
    }
}
