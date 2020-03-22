<?php

namespace Tests\Controller;

use App\Controller\LogonController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\RequestStack;

class LogonControllerTest extends WebTestCase
{
    private ContainerInterface $c;
    private KernelBrowser $client;
    private Crawler $crawler;
    private string $user;
    private string $pw;

    protected function setUp(): void
    {
        global $kernel;

        parent::setUp();

        $this->client = static::createClient(
            [
                'environment' => 'test',
                'debug' => true,
            ]
        );

        $kernel = $this->client->getKernel();

        $this->c = self::$container;

    }

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

    private function getNamePW($paramStr) {
        $cred = self::$container->getParameter($paramStr);

        $this->user = $cred['user'];
        $this->pw = $cred['pw'];

    }
    private function login($user, $pwd)
    {
        $this->crawler = $this->client->request('GET', '/');

        $this->formLogin($user, $pwd);

    }

    private function formLogin($user, $pwd)
    {

        $form = $this->crawler->selectButton("Submit")->form();
        $form['user'] = $user;
        $form['passwd'] = $pwd;
        $this->client->submit($form);

    }

}
