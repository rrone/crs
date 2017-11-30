<?php
namespace App\Action\Reports;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractController;

class ReportsController extends AbstractController
{
    /* @var ReportsView */
    private $reportsView;

    public function __construct(Container $container, ReportsView $reportsView)
    {
        parent::__construct($container);

        $this->reportsView = $reportsView;

    }
    public function __invoke(Request $request, Response $response, $args)
    {
        parent::__invoke($request,  $response, $args);

        $this->reportsView->handler($request, $response);

        if(!$this->isAuthorized()) {
            return $response->withRedirect($this->getBaseURL('logon'));
        };

        $this->logStamp($request);

        $this->reportsView->render($response);

        return $response;
    }
}


