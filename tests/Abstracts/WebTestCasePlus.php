<?php

namespace Tests\Abstracts;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;

class WebTestCasePlus extends WebTestCase
{
    protected ContainerInterface $c;
    protected KernelBrowser $client;
    protected Crawler $crawler;
    protected string $userName;
    protected string $pw;

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

        $this->client->catchExceptions(false);

        $kernel = $this->client->getKernel();

        $this->c = self::$container;

    }

    protected function getNamePW($paramStr = null)
    {
        if (empty($paramStr)) {
            $this->userName = '';
            $this->pw = '';
        }

        $cred = self::$container->getParameter($paramStr);

        $this->userName = $cred['user'];
        $this->pw = $cred['pw'];

    }

    protected function submitLoginForm($userName, $pwd)
    {
        $this->client->request('GET', '/end');
        $this->crawler = $this->client->followRedirect();

        $form = $this->crawler->selectButton("Logon")->form([
            'user' => $userName,
            'passwd' => $pwd
        ]);

        $this->client->submit($form);

    }

    protected function submitAdminForm($btn = null, $txt = '', $userName = '')
    {
        $this->getNamePW('admin_test');
        $this->submitLoginForm($this->userName, $this->pw);
        $this->crawler = $this->client->request('GET', '/admin');

        $form = $this->crawler->selectButton($btn)->form();

        switch ($btn) {
            case 'Add User':
                $form['userName'] = $userName;
                $form['newPassword'] = $txt;
                break;
            case 'Update':
                $form['selectAssignor']->select($userName);
                $form['passwordInput'] = $txt;
                $btn = null;
                break;
            case 'Add to Log':
                $form['logNote'] = $txt;
                break;
            case 'Done':
                break;
        }

        if(!empty($btn)) {
            $this->client->submit($form);
        }

    }

    protected function verifyLink($name, $page)
    {
        $link = $this->crawler->selectLink($name)->link();
        $uri = $this->client->click($link)->getUri();
        $this->assertEquals("http://localhost/$page", $uri);

    }

}
