<?php

namespace Tests\Controller;

use App\Repository\DataWarehouse;
use Tests\Abstracts\WebTestCasePlus;

class AdminControllerTest extends WebTestCasePlus
{

    /**
     * @dataProvider provideAdminUrls
     * @param $url
     */
    public function testAdminSuccessful($url)
    {
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function provideAdminUrls()
    {
        yield ['/admin'];
    }

    public function testAdminAsAnonymous()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/admin');
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->crawler = $this->client->followRedirect();
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        $this->crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());
    }

    public function testAdminAsUser()
    {
        // invoke the controller action and test it
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());
    }

    public function testAdminAsAdmin()
    {
        // invoke the controller action and test it
        $this->getNamePW('admin_test');
        $this->submitLoginForm($this->userName, $this->pw);

        $this->crawler = $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        // verify view
        $this->client->request('GET', '/admin');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());
        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h1>Administrative Functions</h1>", $view);
    }

    public function testNewPW()
    {
        global $kernel;

        $conn = $kernel->getContainer()->get('doctrine.dbal.default_connection');
        $dw = new DataWarehouse($conn);

        $userName = 'userName';
        $pwd = 'password';

        $this->submitAdminForm("Add User", $pwd, $userName);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());

        $u = $dw->getUserByName($userName);
        $this->assertNotEmpty($u);
        if(!empty($u)) {
            $this->assertEquals($u->name, $userName);
            $this->assertTrue(password_verify($pwd, $u->hash));
        }
        $dw->removeUser($u);

        $conn->close();

    }

    public function xtestPWChange()
    {
        $this->getNamePW('user_test');
        $this->pw = 'Area--';

        $this->submitAdminForm("Update", $this->pw, $this->userName);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());

        $this->submitLoginForm($this->userName, $this->pw);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());

        $this->getNamePW('user_test');
        $this->submitAdminForm("Update", $this->pw, $this->userName);

    }

    public function testLogNote()
    {
        $this->submitAdminForm("Add to Log", 'TEST: testLogNote: add to log');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());
    }

    public function testDoneButton()
    {
        // invoke the controller action and test it
        $this->submitAdminForm('Done');

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    public function testLogExportAsUser()
    {
        // invoke the controller action and test it
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->client->request('GET', '/log');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

    }

    public function testLogExportAsAdmin()
    {
        // invoke the controller action and test it
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->client->request('GET', '/log');

        $rpt = $this->client->getResponse()->headers->get('content-type');
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $rpt);
    }


}