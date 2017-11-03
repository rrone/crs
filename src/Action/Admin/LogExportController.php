<?php
namespace App\Action\Admin;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractController;

class LogExportController extends AbstractController
{
    private $exporter;

    public function __construct(Container $container, LogExport $logExport)
    {
        parent::__construct($container);

        $this->exporter = $logExport;
    }
    public function __invoke(Request $request, Response $response, $args)
    {
        if(!$this->isAuthorized()) {
            return $response->withRedirect($this->getBaseURL('logonPath'));
        };

        if (!$this->user->admin) {
            return $response->withRedirect($this->getBaseURL('greetPath'));
        }

        $this->logStamp($request);

        $request = $request->withAttributes([
            'user' => $this->user,
        ]);

        $response = $this->exporter->handler($request, $response);

        return $response;
    }
}
