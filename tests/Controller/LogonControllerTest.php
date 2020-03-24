<?php

namespace Tests\Controller;

use App\Controller\LogonController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Tests\Abstracts\WebTestCasePlus;
use Symfony\Component\HttpFoundation\RequestStack;

class LogonControllerTest extends WebTestCasePlus
{

    public function testRoot()
    {
        // instantiate the controller
        $rs = new RequestStack();
        $controller = new LogonController($rs);
        $this->assertTrue($controller instanceof AbstractController);

        // instantiate the view and test it
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/logon');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    public function testLogonAsUser()
    {
        $this->getNamePW('user_test');

        $this->login($this->user, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->crawler = $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    public function testLogonAsUserWithBadPW()
    {
        $this->getNamePW('user_test');
        $pw = '';

        $this->login($this->user, $pw);

        $this->assertFalse($this->client->getResponse()->isRedirection());

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("Unrecognized password for $this->user", $view);

    }


    public function testLogonAsAdmin()
    {
        $this->getNamePW('admin_test');

        $this->login($this->user, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->crawler = $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    public function testLogonAsDeveloper()
    {
        $this->getNamePW('dev_test');

        $this->login($this->user, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->crawler = $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

}
