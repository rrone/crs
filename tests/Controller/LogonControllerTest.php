<?php

namespace Tests\Controller;

use App\Controller\LogonController;
use Generator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Tests\Abstracts\WebTestCasePlus;
use Symfony\Component\HttpFoundation\RequestStack;

class LogonControllerTest extends WebTestCasePlus
{
    /**
     * @runInSeparateProcess
     * @dataProvider provideHomeUrls
     * @param $url
     */
    public function testLogonSuccessful($url)
    {
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function provideHomeUrls(): Generator
    {
        yield ['/'];
        yield ['/logon'];
    }

    /**
     * @runInSeparateProcess
     * @dataProvider provideUnauthUrls
     * @param $url
     */
    public function testLogonUnsuccessful($url)
    {
        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/logon', $this->client->getRequest()->getPathInfo());
    }

    public function provideUnauthUrls(): Generator
    {
        yield ['/log'];
    }


    public function testController()
    {
        // instantiate the controller
        $rs = new RequestStack();
        $controller = new LogonController($rs);
        $this->assertTrue($controller instanceof AbstractController);
    }

    /**
     * @runInSeparateProcess
     */
    public function testRoot()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/logon');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

    }

    /**
     * @runInSeparateProcess
     */
    public function testLogonAsUser()
    {
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    /**
     * @runInSeparateProcess
     */
    public function testLogonAsUserWithBadPW()
    {
        $this->getNamePW('user_test');
        $pw = '';

        $this->submitLoginForm($this->userName, $pw);

        $this->assertFalse($this->client->getResponse()->isRedirection());

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("Unrecognized password for $this->userName", $view);

    }


    /**
     * @runInSeparateProcess
     */
    public function testLogonAsAdmin()
    {
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    /**
     * @runInSeparateProcess
     */
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
