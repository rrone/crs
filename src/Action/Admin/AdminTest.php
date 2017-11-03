<?php
namespace Tests;

use App\Action\Admin\AdminController;
use App\Action\Admin\AdminView;
use App\Action\AbstractController;
use App\Action\AbstractView;
use App\Action\AbstractExporter;
use App\Action\Admin\LogExportController;
use App\Action\Admin\LogExport;

class AdminTest extends AppTestCase
{
    protected $testUri;
    protected $_object;

    public function setUp()
    {
//     Setup App controller
        $this->app = $this->getSlimInstance();
        $this->app->getContainer()['session'] = [
            'authed' => false,
            'user' => null,
        ];

        $this->client = new AppWebTestClient($this->app);

        $this->testUri = '/adm';

    }

    public function testAdminAsAnonymous()
    {
        // instantiate the view and test it

        $view = new AdminView($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractView);

        // instantiate the controller

        $controller = new AdminController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get($this->testUri);
        $url = implode($response->getHeader('Location'));

        $this->assertEquals('/reports', $url);
    }

    public function testAdminAsUser()
    {
        // instantiate the view and test it

        $view = new AdminView($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractView);

        // instantiate the controller

        $controller = new AdminController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $user = $this->config['user_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get($this->testUri);
        $url = implode($response->getHeader('Location'));

        $this->assertEquals('/reports', $url);
    }

    public function testAdminAsAdmin()
    {
        // instantiate the view and test it

        $view = new AdminView($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractView);

        // instantiate the controller

        $controller = new AdminController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $user = $this->config['admin_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get($this->testUri);
        $view = (string)$response->getBody();

        $this->assertContains("<h1>Administrative Functions</h1>", $view);
    }

    public function testLogExportAsUser()
    {
        // instantiate the view and test it

        $view = new LogExport($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractExporter);

        // instantiate the controller

        $controller = new LogExportController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $user = $this->config['user_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/adm/log');

        $url = implode($response->getHeader('Location'));

        $this->assertEquals('/reports', $url);
    }

    public function testLogExportAsAdmin()
    {
        // instantiate the view and test it

        $view = new LogExport($this->c, $this->dw);
        $this->assertTrue($view instanceof AbstractExporter);

        // instantiate the controller

        $controller = new LogExportController($this->c, $view);
        $this->assertTrue($controller instanceof AbstractController);

        // invoke the controller action and test it

        $user = $this->config['admin_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/adm/log');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=Access_Log', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    public function testPOSTPWChange()
    {
        /* TODO: Add POST Test for PW change */
    }

    public function testPOSTLogMemo()
    {
        /* TODO: Add POST Test for Log memo */
    }

    public function testDoneButton()
    {
        /* TODO: Add POST Test for Done button */
    }

}