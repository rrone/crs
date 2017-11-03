<?php
namespace Tests;

use App\Action\AbstractController;
use App\Action\AbstractView;
use App\Action\Reports\ReportsController;
use App\Action\Reports\ReportsView;

class ReportsTest extends AppTestCase
{
    public function setUp()
    {
//     Setup App controller
        $this->app = $this->getSlimInstance();
        $this->app->getContainer()['session'] = [
            'authed' => false,
            'user' => null,
        ];

        $this->client = new AppWebTestClient($this->app);

    }

    public function testReportsAsAnonymous()
    {
        // instantiate the view and test it

        $view = new ReportsView($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractView);

        // instantiate the controller

        $controller = new ReportsController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/reports');
        $url = implode($response->getHeader('Location'));

        $this->assertEquals('/', $url);
    }

    public function testReportsAsUser()
    {
        // instantiate the view and test it

        $view = new ReportsView($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractView);

        // instantiate the controller

        $controller = new ReportsController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $user = $this->config['user_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        $view = $this->client->post('/reports');
        $this->assertContains("<h3 class=\"center\">Welcome $user Assignor</h3>",$view);
    }

    public function testReportsAsAdmin()
    {
        // instantiate the view and test it

        $view = new ReportsView($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractView);

        // instantiate the controller

        $controller = new ReportsController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it
        $user = $this->config['admin_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

//        $view = $this->client->get('/reports');
//        $this->assertContains("<h3 class=\"center\">Welcome $user</h3>",$view);
//        $this->assertContains("<h3 class=\"center\"><a href=/editgame>Edit matches</a>",$view);
    }

}