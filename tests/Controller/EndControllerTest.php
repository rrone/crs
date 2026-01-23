<?php

namespace Tests\Controller;

use App\Controller\EndController;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Abstracts\WebTestCasePlus;

class EndControllerTest extends WebTestCasePlus
{
//    public function testController()
//    {
//        // instantiate the controller
//        $rs = new RequestStack();
//        $controller = new EndController($rs);
//        $this->assertTrue($controller instanceof AbstractController);
//    }
    /**
     * @return void
     */
    #[WithoutErrorHandler]
    public function testCodeThatSetsCustomErrorHandler(): void
    {
        // Code that sets and does not remove its own error handler
        set_error_handler(function () { /* ... */ });
        trigger_error('An error', E_USER_NOTICE);
        // PHPUnit will not complain about the unremoved handler here
    }

    #[RunInSeparateProcess]
    public function testEnd()
    {
        $this->getNamePW('user_test');

        // logon as user
        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        // logout
        $this->client->request('GET', '/end');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());
    }
}
