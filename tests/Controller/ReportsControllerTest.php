<?php

namespace Tests\Controller;

use App\Controller\ReportsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Abstracts\WebTestCasePlus;

class ReportsControllerTest extends WebTestCasePlus
{
    /**
     * @dataProvider providePageUrls
     * @param $url
     */
    public function testPageIsSuccessful($url)
    {
        $this->getNamePW('admin_test');
        $this->submitLoginForm($this->userName, $this->pw);

        $page = $this->client->request('GET', $url);

        switch ($url) {
            case '/reports':
            case '/admin':
                $this->assertResponseIsSuccessful();
                break;
            default:
                $ref = $page->getUri();
                $this->assertStringContainsString($url, $ref);
        }
    }

    public function providePageUrls()
    {
        yield ['/'];
        yield ['/logon'];
        yield ['/reports'];
        yield ['/admin'];

    }

    /**
     * @dataProvider provideReportUrls
     * @param $url
     */
    public function testReports($url)
    {
        $this->getNamePW('admin_test');
        $this->submitLoginForm($this->userName, $this->pw);

        $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        $rpt = $this->client->getResponse()->headers->get('content-type');
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $rpt);
    }

    public function provideReportUrls()
    {
        yield ['/bshca'];
        yield ['/ra'];
        yield ['/ri'];
        yield ['/rie'];
        yield ['/ruc'];
        yield ['/urr'];
        yield ['/nra'];

    }

    /**
     * @dataProvider provideRedirectUrls
     * @param $url
     */
    public function testPageRedirects($url)
    {

        $this->client->request('GET', $url);

        $this->assertResponseRedirects();
    }

    public function provideRedirectUrls()
    {
        yield ['/end'];
        yield ['/reports'];
        yield ['/bshca'];
        yield ['/ra'];
        yield ['/ri'];
        yield ['/rie'];
        yield ['/ruc'];
        yield ['/urr'];
        yield ['/nra'];

//        //unused reports defined in ExportXl
//        yield ['/hrc'];
//        yield ['/nocerts'];
//        yield ['/rcdc'];
//        yield ['/rsh'];

        //bad link
        yield ['/xyz'];

    }

    public function testController()
    {
        // instantiate the controller
        $rs = new RequestStack();
        $controller = new ReportsController($rs);
        $this->assertTrue($controller instanceof AbstractController);
    }

    public function testReportsAsAnonymous()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/reports');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());
    }

    public function testReportsAsUser()
    {
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->crawler = $this->client->followRedirect();

        // verify view
        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

        // verify links & test exports
//        $this->verifyLink('Composite Referee Certifications (Highest Certification, Safe Haven & Concussion Awareness)', 'bshca');
        $this->verifyLink('Referee Assessors', 'ra');
        $this->verifyLink('Referee Instructors', 'ri');
        $this->verifyLink('Referee Instructor Evaluators', 'rie');
        $this->verifyLink('Referee Upgrade Candidates', 'ruc');
        $this->verifyLink('Unregistered Referees', 'urr');

        $this->assertEmpty($this->crawler->selectLink('National Referee Assessors')->getNode(0));

    }

    public function testReportsAsAdmin()
    {
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->crawler = $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

        // verify links
//        $this->verifyLink('Composite Referee Certifications (Highest Certification, Safe Haven & Concussion Awareness)', 'bshca');
//        $this->verifyLink('Referee Assessors', 'ra');
//        $this->verifyLink('Referee Instructors', 'ri');
//        $this->verifyLink('Referee Instructor Evaluators', 'rie');
//        $this->verifyLink('Referee Upgrade Candidates', 'ruc');
//        $this->verifyLink('Unregistered Referees', 'urr');
        $this->verifyLink('National Referee Assessors', 'nra');
    }

}
