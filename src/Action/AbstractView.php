<?php

namespace App\Action;

use Slim\Container;
use Psr\Container\ContainerInterface;
use Slim\Views\Twig;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class AbstractView
{
    /* @var Container */
    protected $container;

    /* @var Twig */
    protected $view;

    /* @var DataWarehouse */
    protected $dw;

    //view variables
    protected $user;
    protected $event;

    //view variables
    protected $page_title;
    protected $dates;
    protected $location;
    protected $msg;
    protected $msgStyle;
    protected $menu;

    protected $uri;


    public function __construct(ContainerInterface $container, DataWarehouse $dataWarehouse)
    {
        $this->container = $container;
        $this->view = $this->container->get('view');
        $this->dw = $dataWarehouse;

        $this->page_title = "Section 1: Certification Reporting System";
    }

    abstract protected function render(Response &$response);

    protected function isRepost(Request $request)
    {
        if ($request->isPost()) {
            $_POST = $request->getParsedBody();

            if (isset($_SESSION['postdata'])) {
                if ($_POST == $_SESSION['postdata']) {
                    return true;
                } else {
                    $_SESSION['postdata'] = $_POST;
                }
            } else {
                $_SESSION['postdata'] = $_POST;
            }
        }

        return false;
    }

    protected function getBaseURL($path)
    {
        $request = $this->container->get('request');
        $baseUri = $request->getUri()->getBasePath().$this->container->get($path);

        return $baseUri;
    }

}