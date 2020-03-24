<?php

namespace Tests\Abstracts;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DomCrawler\Crawler;

class WebTestCasePlus extends WebTestCase
{
    protected ContainerInterface $c;
    protected KernelBrowser $client;
    protected Crawler $crawler;
    protected string $user;
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

    protected function getNamePW($paramStr)
    {
        $cred = self::$container->getParameter($paramStr);

        $this->user = $cred['user'];
        $this->pw = $cred['pw'];

    }

    protected function login($user, $pwd)
    {
        $this->crawler = $this->client->request('GET', '/');

        $this->formLogin($user, $pwd);

    }

    protected function formLogin($user, $pwd)
    {

        $form = $this->crawler->selectButton("Submit")->form();
        $form['user'] = $user;
        $form['passwd'] = $pwd;
        $this->client->submit($form);

    }

    protected function linkYieldsReport($name, $page)
    {
        $c = $this->client;
        $link = $this->crawler->selectLink($name)->link();
        $uri = $c->click($link)->getUri();
        $this->assertEquals("http://localhost/$page", $uri);

        $c->request('GET', $uri);
        $rpt = $c->getResponse()->headers->get('content-type');
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $rpt);

    }


}
