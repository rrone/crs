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
        $response = (object)$this->client->get('/ra');

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

        $this->HighestRefereeCertification();
        $this->RefereeAssessors();
        $this->RefereeInstructors();
        $this->RefereeInstructorEvaluators();
        $this->RefereeUpgradeCandidates();
        $this->UnregisteredReferees();
        $this->NoCertsReferees();
        $this->CDCReferees();
        $this->SafeHavenReferees();
        $this->CompositeRefCerts();
        $this->NationalRefereeAssessorsNotAllowed();
        $this->UndefinedCall();
    }

    public function testExportAsAdmin()
    {
        // invoke the controller action and test it

        $user = $this->config['admin_test']['user'];

        $this->client->app->getContainer()['session'] = [
            'authed' => true,
            'user' => $this->dw->getUserByName($user),
        ];

        $this->HighestRefereeCertification();
        $this->RefereeAssessors();
        $this->RefereeInstructors();
        $this->RefereeInstructorEvaluators();
        $this->RefereeUpgradeCandidates();
        $this->UnregisteredReferees();
        $this->NoCertsReferees();
        $this->CDCReferees();
        $this->SafeHavenReferees();
        $this->CompositeRefCerts();
        $this->NationalRefereeAssessorsAllowed();
        $this->UndefinedCall();
    }

    protected function HighestRefereeCertification()
    {
        //Highest Referee Certification
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/hrc?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);

        return;
    }

    protected function RefereeAssessors()
    {
        //Referee Assessors
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/ra?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function RefereeInstructors()
    {
        //Referee Instructors
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/ri?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function RefereeInstructorEvaluators()
    {
        //Referee Instructor Evaluators
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/rie?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function RefereeUpgradeCandidates()
    {
        //Referee Upgrade Candidates
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/ruc?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function UnregisteredReferees()
    {
        //Unregistered Referees
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/urr?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function NoCertsReferees()
    {
        //Unregistered Referees
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/nocerts?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function CDCReferees()
    {
        //Concussion Training for Referees
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/rcdc?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function SafeHavenReferees()
    {
        //Safe Haven Referees
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/rsh?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function CompositeRefCerts()
    {
        //Safe Haven Referees
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/bshca?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);
    }

    protected function NationalRefereeAssessorsNotAllowed()
    {
        //National Referee Assessors -- not allowed
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/nra?20');

        $view = (string)$response->getBody();
        $this->assertEquals('', $view);

        $url = implode($response->getHeader('Location'));
        $this->assertEquals('/', $url);
    }

    protected function NationalRefereeAssessorsAllowed()
    {
        //National Referee Assessors -- allowed
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/nra?20');

        $contentType = $response->getHeader('Content-Type')[0];
        $cType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        $this->assertEquals($cType, $contentType);

        $contentDisposition = $response->getHeader('Content-Disposition')[0];
        $this->assertContains('attachment; filename=', $contentDisposition);
        $this->assertContains('.xlsx', $contentDisposition);

    }

    protected function UndefinedCall()
    {
        //Safe Haven Referees
        $this->client->returnAsResponseObject(true);
        $response = (object)$this->client->get('/xxx?null');

        $this->assertEquals(isset($response->getHeader('Content-Type')[0]), false);

        $response = (object)$this->client->get('/xxx');

        $this->assertEquals(isset($response->getHeader('Content-Type')[0]), false);

    }

}