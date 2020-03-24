<?php
namespace Tests\Controller;

use App\Controller\AdminController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Abstracts\WebTestCasePlus;

class AdminControllerTest extends WebTestCasePlus
{
    protected $testUri;
    protected $_object;

    /**
     * @dataProvider provideAdminUrls
     */
    public function testAdminSuccessful($url)
    {
        $this->getNamePW('admin_test');

        $this->login($this->user, $this->pw);

        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function provideAdminUrls()
    {
        yield ['/admin'];
    }


//    public function testAdminAsAnonymous()
//    {
//        // instantiate the controller
//        $rs = new RequestStack();
//        $controller = new AdminController($rs);
//        $this->assertTrue($controller instanceof AbstractController);
//
//        $this->assertEquals('/reports', $url);
//    }
//
//    public function testAdminAsUser()
//    {
//        // instantiate the view and test it
//
//        $view = new AdminView($this->c, $this->dw);
//        $this->assertTrue($view instanceof AbstractView);
//
//        // instantiate the controller
//
//        $controller = new AdminController($this->c, $view);
//        $this->assertTrue($controller instanceof AbstractController);
//
//        // invoke the controller action and test it
//
//        $user = $this->config['user_test']['user'];
//
//        $this->client->app->getContainer()['session'] = [
//            'authed' => true,
//            'user' => $this->dw->getUserByName($user),
//        ];
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->get($this->testUri);
//        $url = implode($response->getHeader('Location'));
//
//        $this->assertEquals('/reports', $url);
//    }
//
//    public function testAdminAsAdmin()
//    {
//        // instantiate the view and test it
//
//        $view = new AdminView($this->c, $this->dw);
//        $this->assertTrue($view instanceof AbstractView);
//
//        // instantiate the controller
//
//        $controller = new AdminController($this->c, $view);
//        $this->assertTrue($controller instanceof AbstractController);
//
//        // invoke the controller action and test it
//
//        $user = $this->config['admin_test']['user'];
//
//        $this->client->app->getContainer()['session'] = [
//            'authed' => true,
//            'user' => $this->dw->getUserByName($user),
//        ];
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->get($this->testUri);
//        $view = (string)$response->getBody();
//
//        $this->assertContains("<h1>Administrative Functions</h1>", $view);
//    }
//
//    public function testLogExportAsUser()
//    {
//        // instantiate the view and test it
//
//        $view = new LogExport($this->c, $this->dw);
//        $this->assertTrue($view instanceof AbstractExporter);
//
//        // instantiate the controller
//
//        $controller = new LogExportController($this->c, $view);
//        $this->assertTrue($controller instanceof AbstractController);
//
//        // invoke the controller action and test it
//
//        $user = $this->config['user_test']['user'];
//
//        $this->client->app->getContainer()['session'] = [
//            'authed' => true,
//            'user' => $this->dw->getUserByName($user),
//        ];
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->get('/adm');
//
//        $url = implode($response->getHeader('Location'));
//
//        $this->assertEquals('/reports', $url);
//    }
//
//    public function testLogExportAsAdmin()
//    {
//        // instantiate the view and test it
//
//        $view = new LogExport($this->c, $this->dw);
//        $this->assertTrue($view instanceof AbstractExporter);
//
//        // instantiate the controller
//
//        $controller = new LogExportController($this->c, $view);
//        $this->assertTrue($controller instanceof AbstractController);
//
//        // invoke the controller action and test it
//
//        $user = $this->config['admin_test']['user'];
//
//        $this->client->app->getContainer()['session'] = [
//            'authed' => true,
//            'user' => $this->dw->getUserByName($user),
//        ];
//
//        $this->client->returnAsResponseObject(true);
//        $response = (object)$this->client->get('/log');
//
//        $contentType = $response->getHeader('Content-Type')[0];
//        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
//        $this->assertEquals($cType, $contentType);
//
//        $contentDisposition = $response->getHeader('Content-Disposition')[0];
//        $this->assertContains('attachment; filename=Log', $contentDisposition);
//        $this->assertContains('.xlsx', $contentDisposition);
//    }

//    public function testPOSTPWChange()
//    {
//        /* TODO: Add POST Test for PW change */
//    }
//
//    public function testPOSTLogMemo()
//    {
//        /* TODO: Add POST Test for Log memo */
//    }
//
//    public function testDoneButton()
//    {
//        /* TODO: Add POST Test for Done button */
//    }

}
