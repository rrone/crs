<?php
namespace Tests;

class ExportsTest extends AppTestCase
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
    
    public function testExportAsAnonymous()
    {
        // invoke the controller action and test it

        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/export/ra');

        $view = (string)$response->getBody();
        $this->assertEquals('', $view);

        $url = implode($response->getHeader('Location'));
        $this->assertEquals('/', $url);
    }

    public function testExportAsUser()
    {
        // invoke the controller action and test it

        $user = $this->config['user_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        //Highest Referee Certification
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/export/hrc?100');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=Report_', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);

        //Referee Assessors
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/export/ra');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=Report_', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);

        //Referee Instructors
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/export/ri');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=Report_', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    public function testExportAsAdmin()
    {
        // invoke the controller action and test it

        $user = $this->config['admin_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        //Highest Referee Certification
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/export/hrc?100');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=Report_', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);

        //Referee Assessors
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/export/ra');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=Report_', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);

        //Referee Instructors
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/export/ri');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=Report_', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);    }


}