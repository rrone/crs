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
                'message' => "<h3 class=\"center\">Message</h3>\n"
            )
        );
var_dump($this->user);
var_dump($content);
        $this->view->render($response, 'reports.html.twig', $content);
    }
    protected function renderView()
    {
        $html = null;

        if (!empty($this->user)) {

//                $uname = $this->user->name;

                $html .= "<h3 class=\"center\">CURRENT STATUS</h3>\n";
                $html .= "<h3 class=\"center\">The following reports are available.</h3>\n";


            $html .= "<h3 class=\"center\"><a href=" . $this->getBaseURL('endPath') . ">Log Off</a></h3>";

        }

        return $html;

    }
}