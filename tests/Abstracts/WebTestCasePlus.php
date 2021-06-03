<?php

namespace Tests\Abstracts;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class WebTestCasePlus extends WebTestCase
{
    protected ContainerInterface $c;
    protected ?KernelBrowser $client;
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
        $this->client->followRedirects(true);
        $crawler = $this->client->request('GET', '/end');
        $this->client->followRedirects(false);

        $form = $crawler->selectButton("Logon")->form([
            'user' => $userName,
            'passwd' => $pwd,
        ]);

        $this->client->submit($form);
    }

    protected function submitAdminForm($btn = null, $txt = '', $userName = '')
    {
        $this->getNamePW('dev_test');
        $this->submitLoginForm($this->userName, $this->pw);
        $crawler = $this->client->request('GET', '/admin');

        $form = $crawler->selectButton($btn)->form();

        switch ($btn) {
            case 'btnAddUser':
                $form['userName'] = $userName;
                $form['newPassword'] = $txt;
                break;
            case 'btnUpdate':
                $form['selectAssignor']->setValue($userName);
                $form['passwordInput'] = $txt;
                break;
            case 'btnLogItem':
                $form['logNote'] = $txt;
                break;
            case 'btnExportLog':
            case 'btnDone':
                break;
        }

        if (!empty($btn)) {
            $this->client->submit($form);
        }

    }

    protected function verifyLink($crawler, $name, $page)
    {
        $link = $crawler->selectLink($name)->link();
        $uri = $this->client->click($link)->getUri();
        $this->assertEquals("http://localhost/$page", $uri);

    }

}
