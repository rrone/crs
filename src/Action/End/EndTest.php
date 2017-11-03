<?php
namespace Tests;

use App\Action\End\EndController;
use App\Action\AbstractController;
use Slim\Http\Response;

class EndTest extends AppTestCase
{
    public function setUp()
    {
        $this->app = $this->getSlimInstance();

        $this->client = new AppWebTestClient($this->app);
    }

    public function testEnd()
    {
        // instantiate the controller & test it
        $controller = new EndController($this->c);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $request  = $this->request('GET', '/end');
        $response = new Response();

        $app = $this->client->app;
        $response = $app($request, $response);

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(302, $response->getStatusCode());

        /** @noinspection PhpUndefinedMethodInspection */
        $url = implode($response->getHeader('Location'));

        $this->assertEquals('/', $url);
    }

}