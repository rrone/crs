<?php

namespace Tests\Controller;

use App\Controller\LogonController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Tests\Abstracts\WebTestCasePlus;
use Symfony\Component\HttpFoundation\RequestStack;

class LogonControllerTest extends WebTestCasePlus
{
    /**
     * @dataProvider provideHomeUrls
     * @param $url
     */
    public function testLogonSuccessful($url)
    {
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function provideHomeUrls()
    {
        yield ['/'];
        yield ['/logon'];
    }

    public function testController()
    {
        // instantiate the controller
        $rs = new RequestStack();
        $controller = new LogonController($rs);
        $this->assertTrue($controller instanceof AbstractController);
    }

    public function testRoot()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/logon');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    public function testLogonAsUser()
    {
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    public function testLogonAsUserWithBadPW()
    {
        $this->getNamePW('user_test');
        $pw = '';

        $this->submitLoginForm($this->userName, $pw);

        $this->assertFalse($this->client->getResponse()->isRedirection());

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("Unrecognized password for $this->userName", $view);

    }


    public function testLogonAsAdmin()
    {
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    public function testLogonAsDeveloper()
    {
        $this->getNamePW('dev_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

}
