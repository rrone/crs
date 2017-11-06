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
        $html = $this->renderView();

        $content = array(
            'view' => array(
                'admin' => $this->user->admin,
                'content' => $html,
                'message' => null,
            ),
        );
        $this->view->render($response, 'reports.html.twig', $content);
    }

    protected function renderView()
    {
        $html = null;

//        $uname = $this->user->name;

//        $html .= "<h3 class=\"center\">Welcome, $uname</h3>\n";

        $html .= <<<EOD
<div class="legend">
    <h3>Notes on these reports</h3>
        <ul>
            <li>All reports are delivered as Excel workbooks.</li>
            <li>All report data is extracted from the 83 Blue Sombrero Portals within Section 1 on Friday.</li>
            <li>Suggestions for other reports should be sent to <a href="mailto:{{ sra_email }}?{{ 
            subject }}">Section 1 Referee Administrator</a>.</li>
            <li>Blue Sombrero/AYSOU information/status of known issues are published on 
       <a href="http://www.aysonational.org/Default.aspx?tabid=917680" target="_blank">AYSO's Blue Sombrero Support 
       Page</a>.</li>
            <li>Any errors/ommissions should be address by a problem report to <a 
            href="mailto:aysosupport@bluesombrero.com">aysosupport@bluesombrero.com</a>.</li>
            <li>Having an Instructor certification in Blue Sombrero does not ensure Instructor status in 
            AYSOU.ORG.  You also need to be included on the Instructor list in AYSOU.  You can request inclusion 
            by a request to <a href="mailto:support@ayso.org?subject=Mad as hell and not going to take it anymore...">support@ayso.org</a>.</li>
            <li>Don't be surprised if it takes months to get it corrected.  Be the squeaky wheel.</li>
        </ul>

</div>
<hr>
EOD;

        $html .= "<h3 class=\"center\">Available reports for volunteers in Section 1:</h3>\n";

        $href = $this->getBaseURL('hrc');
        $html .= "<h3 class=\"center\"><a  href=$href >Highest Referee Certification</a></h3>\n";
        $html .= "<div class='clear-fix'></div>\n";
        $html .= "</h3>\n";

        $href = $this->getBaseURL('ra');
        $html .= "<h3 class=\"center\"><a  href=$href >Referee Assessors</a></h3>\n";
        $html .= "<div class='clear-fix'></div>\n";
        $html .= "</h3>\n";

        $href = $this->getBaseURL('ri');
        $html .= "<h3 class=\"center\"><a  href=$href >Referee Instructors</a></h3>\n";
        $html .= "<div class='clear-fix'></div>\n";
        $html .= "</h3>\n";

        $href = $this->getBaseURL('rie');
        $html .= "<h3 class=\"center\"><a  href=$href >Referee Instructor Evaluators</a></h3>\n";
        $html .= "<div class='clear-fix'></div>\n";
        $html .= "</h3>\n";
        $html .= "<hr>\n";

        $href = $this->getBaseURL('end');
        $html .= "<h3 class=\"center\"><a href=$href>Log Off</a></h3>\n";

        return $html;

    }
}
