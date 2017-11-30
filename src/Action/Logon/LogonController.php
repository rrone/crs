<?php
namespace App\Action\Logon;

use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use App\Action\AbstractController;
use Dflydev\FigCookies\FigResponseCookies;
use Dflydev\FigCookies\SetCookie;

class LogonController extends AbstractController
{
    /* @var LogonView */
    private $logonView;

    public function __construct(Container $container, LogonView $logonView)
    {

        parent::__construct($container);

        $this->logonView = $logonView;
    }

    public function __invoke(Request $request, Response $response, $args)
    {

        $this->logonView->handler($request, $response);
        $this->SESSION = $this->logonView->SESSION;
        var_dump($this->logonView->SESSION);

        if ($this->isAuthorized()) {
            $this->logStamp($request);

            $response = FigResponseCookies::set($response, SetCookie::create('CRSID')
                ->withValue($this->SESSION['user']->name)
            );

            return $response->withRedirect($this->getBaseURL('reports'));
        }

        $response = $this->logonView->render($response);

        return $response;
    }
}
