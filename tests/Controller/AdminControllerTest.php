<?php

namespace Tests\Controller;

use App\Repository\DataWarehouse;
use Generator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tests\Abstracts\WebTestCasePlus;

class AdminControllerTest extends WebTestCasePlus
{

    /**
     * @runInSeparateProcess
     * @Security("!has_role('ROLE_USER')")
     * @dataProvider provideAdminUrls
     * @param $url
     */
    public function testAdminSuccessful($url)
    {
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->expectException(AccessDeniedException::class);
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function provideAdminUrls(): Generator
    {
        yield ['/admin'];
    }

    /**
     * @runInSeparateProcess
     */
    public function testInvalidLogin()
    {
        // invoke the controller action and test it
        $this->getNamePW('user_test');

        $this->submitLoginForm('Area 1/B', '');
        $view = $this->client->getResponse()->getContent();
        $this->assertStringNotContainsString("Password may not be blank", $view);
    }

    /**
     * @runInSeparateProcess
     */
    public function testAdminAsAnonymous()
    {
        // instantiate the view and test it
        $this->expectException(AccessDeniedException::class);
        $this->client->request('GET', '/admin');
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAdminAsUser()
    {
        // invoke the controller action and test it
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());
    }

    /**
     * @runInSeparateProcess
     */
    public function testAdminAsAdmin()
    {
        // invoke the controller action and test it
        $this->getNamePW('admin_test');
        $this->submitLoginForm($this->userName, $this->pw);

        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        // verify view
        $this->expectException(AccessDeniedException::class);
        $this->client->request('GET', '/admin');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());
        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h1>Administrative Functions</h1>", $view);
    }

    /**
     * @runInSeparateProcess
     */
    public function testNewPW()
    {
        global $kernel;

        $conn = $kernel->getContainer()->get('doctrine.dbal.default_connection');
        $dw = new DataWarehouse($conn);

        $userName = 'userName';
        $pwd = 'password';

        $this->expectException(AccessDeniedException::class);
        $this->submitAdminForm("btnAddUser", $pwd, $userName);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());

        // test user exists
        $this->submitAdminForm("btnAddUser", $pwd, $userName);
        // test blank user name (default)
        $this->submitAdminForm("btnAddUser", $pwd);
        // test blank pw
        $this->submitAdminForm("btnAddUser", '', $userName);

        $u = $dw->getUserByName($userName);
        $this->assertNotEmpty($u);
        if (!empty($u)) {
            $this->assertEquals($u->name, $userName);
            $this->assertTrue(password_verify($pwd, $u->hash));
        }
        $dw->removeUser($u);

        $conn->close();

    }

    /**
     * @runInSeparateProcess
     */
    public function testPWChange()
    {
        $this->getNamePW('user_test');
        $userName = $this->userName;
        $pw = 'Area--';

        $this->submitLoginForm($this->userName, $this->pw);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        $this->client->request('GET', '/end');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();

        $this->expectException(AccessDeniedException::class);
        $this->submitAdminForm("btnUpdate", $pw, $userName);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());

        $this->submitLoginForm($userName, $pw);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        $this->getNamePW('user_test');
        $this->submitAdminForm("btnUpdate", $this->pw, $this->userName);

        $this->submitLoginForm($this->userName, $this->pw);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/reports', $this->client->getRequest()->getPathInfo());

        // test blank pw
        $this->submitAdminForm("btnUpdate", '', $userName);

    }

    /**
     * @runInSeparateProcess
     */
    public function testLogNote()
    {
        $this->expectException(AccessDeniedException::class);
        $this->submitAdminForm("btnLogItem", 'TEST: testLogNote: add to log');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/admin', $this->client->getRequest()->getPathInfo());
    }

    /**
     * @runInSeparateProcess
     */
    public function testDoneButton()
    {
        // invoke the controller action and test it
        $this->expectException(AccessDeniedException::class);
        $this->submitAdminForm('btnDone');

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

    }

    /**
     * @runInSeparateProcess
     */
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

    /**
     * @runInSeparateProcess
     */
    public function testLogExportAsAdmin()
    {
        // invoke the controller action and test it
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->client->request('GET', '/log');

        $rpt = $this->client->getResponse()->headers->get('content-type');
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $rpt);
    }

    /**
     * @runInSeparateProcess
     */
    public function testLogExport()
    {
        $this->expectException(AccessDeniedException::class);
        $this->submitAdminForm('btnExportLog');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $contentType = $this->client->getResponse()->headers->get('content-type');
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $contentType);
    }
}
