<?php
namespace Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LogonControllerTest extends WebTestCase
{
    protected $eventLabel;
    protected $userName;
    protected $passwd;
    protected $client;

    protected function setUp() : void
    {
        global $kernel;

        parent::setUp();

        $this->client = static::createClient([
            'environment' => 'test',
            'debug' => true
        ]);

        $kernel = $this->client->getKernel();
    }

    public function testRoot()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->request('GET', '/logon');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

//         instantiate the controller

//        $controller = new LogonController($this->c);
//        $this->assertTrue($controller instanceof AbstractController);

    }

//    public function testLogonAsUser()
//    {
//        $this->userName = $this->config['user_test']['user'];
//        $this->passwd = $this->config['user_test']['passwd'];
//
//        $url = '/';
//        $headers = array(
//            'cache-control' => 'no-cache',
//            'content-type' => 'multipart/form-data;'
//        );
//        $body = array(
//            'user' => $this->userName,
//            'passwd' => $this->passwd,
//            'Submit' => 'Logon'
//        );
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->post($url, $body, $headers);
//
//        $url = implode($response->getHeader('Location'));
//        $this->assertEquals('/reports', $url);
//
//        $response = (object)$this->client->get($url);
//        $view = (string)$response->getBody();
//        $this->assertContains("<h3>Notes on these reports:</h3>", $view);
//    }
//
//    public function testLogonAsUserWithBadPW()
//    {
//        $this->userName = $this->config['user_test']['user'];
//
//        $url = '/';
//        $headers = array(
//            'cache-control' => 'no-cache',
//            'content-type' => 'multipart/form-data;'
//        );
//        $body = array(
//            'user' => $this->userName,
//            'passwd' => '',
//            'Submit' => 'Logon'
//        );
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->post($url, $body, $headers);
//        $view = (string)$response->getBody();
//        $this->assertContains('<td width="50%"><div class="right">Report Admin: </div></td>', $view);
//        $this->assertContains("Unrecognized password for $this->userName", $view);
//    }
//
//
//    public function testLogonAsAdmin()
//    {
//        $this->userName = $this->config['admin_test']['user'];
//        $this->passwd = $this->config['admin_test']['passwd'];
//
//        $url = '/';
//        $headers = array(
//            'cache-control' => 'no-cache',
//            'content-type' => 'multipart/form-data;'
//        );
//        $body = array(
//            'user' => $this->userName,
//            'passwd' => $this->passwd,
//            'Submit' => 'Logon'
//        );
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->post($url, $body, $headers);
//
//        $url = implode($response->getHeader('Location'));
//        $this->assertEquals('/reports', $url);
//
//        $response = (object)$this->client->get($url);
//        $view = (string)$response->getBody();
//        $this->assertContains("<h3>Notes on these reports:</h3>", $view);
//
//    }
//
//    public function testLogonAsDeveloper()
//    {
//        $this->userName = $this->config['dev_test']['user'];
//        $this->passwd = $this->config['dev_test']['passwd'];
//
//        $url = '/';
//        $headers = array(
//            'cache-control' => 'no-cache',
//            'content-type' => 'multipart/form-data;'
//        );
//        $body = array(
//            'user' => $this->userName,
//            'passwd' => $this->passwd,
//            'Submit' => 'Logon'
//        );
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->post($url, $body, $headers);
//
//        $url = implode($response->getHeader('Location'));
//        $this->assertEquals('/reports', $url);
//
//        $response = (object)$this->client->get($url);
//        $view = (string)$response->getBody();
//        $this->assertContains("<h3>Notes on these reports:</h3>", $view);
//
//    }

}