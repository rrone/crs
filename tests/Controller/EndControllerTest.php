<?php

namespace Tests;

use App\Controller\EndController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Abstracts\WebTestCasePlus;

class EndControllerTest extends WebTestCasePlus
{
    public function testController()
    {
        // instantiate the controller
        $rs = new RequestStack();
        $controller = new EndController($rs);
        $this->assertTrue($controller instanceof AbstractController);
    }

    public function testEnd()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/end');
        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());
    }

}
