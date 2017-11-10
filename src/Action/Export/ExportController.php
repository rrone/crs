<?php

namespace  App\Action\Export;

use Slim\Container;
use Slim\Http\Request as Request;
use Slim\Http\Response as Response;
use App\Action\AbstractController;

class ExportController extends AbstractController
{
    private $exportXl;

	public function __construct(Container $container, ExportXl $exportXl)
    {
		parent::__construct($container);

        $this->exportXl = $exportXl;

    }
    public function __invoke(Request $request, Response $response, $args)
    {

        if(!$this->isAuthorized()) {
            return $response->withRedirect($this->getBaseURL('logon'));
        };

        $this->logStamp($request);

        $request = $request->withAttributes([
            'user' => $this->user,
        ]);

        $response = $this->exportXl->handler($request, $response);

        return $response;
		
    }
}
