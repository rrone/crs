<?php

namespace Tests\Controller;

use App\Controller\LogonController;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Abstracts\WebTestCasePlus;

class LogonControllerTest extends WebTestCasePlus
{
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

    #[DataProvider('provideHomeUrls')]
    #[RunInSeparateProcess]
    public function testLogonSuccessful($url)
    {
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public static function provideHomeUrls(): Generator
    {
        yield ['/'];
        yield ['/logon'];
    }

    #[DataProvider('provideUnauthUrls')]
    #[RunInSeparateProcess]
    public function testLogonUnsuccessful($url)
    {
        $this->client->request('GET', $url);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/logon', $this->client->getRequest()->getPathInfo());
    }

    public static function provideUnauthUrls(): Generator
    {
        yield ['/log'];
    }

//    public function testController()
//    {
//        // instantiate the controller
//        $rs = new RequestStack();
//        $controller = new LogonController($rs);
//        $this->assertTrue($controller instanceof AbstractController);
//    }

    #[RunInSeparateProcess]
    public function testRoot()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/logon');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    #[RunInSeparateProcess]
    public function testLogonAsUser()
    {
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<h3>Notes on these reports:</h3>', $view);
    }

    #[RunInSeparateProcess]
    public function testLogonAsUserWithBadPW()
    {
        $this->getNamePW('user_test');
        $pw = '';

        $this->submitLoginForm($this->userName, $pw);

        $this->assertFalse($this->client->getResponse()->isRedirection());

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("Unrecognized password for $this->userName", $view);
    }

    #[RunInSeparateProcess]
    public function testLogonAsAdmin()
    {
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<h3>Notes on these reports:</h3>', $view);
    }

    #[RunInSeparateProcess]
    public function testLogonAsDeveloper()
    {
        $this->getNamePW('dev_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString('<h3>Notes on these reports:</h3>', $view);
    }
}
