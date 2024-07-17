<?php

namespace Tests\Controller;

use App\Controller\ReportsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Tests\Abstracts\WebTestCasePlus;

class ReportsControllerTest extends WebTestCasePlus
{
    /**
     * @runInSeparateProcess
     * @dataProvider providePageUrls
     * @param $url
     */
    public function testPageIsSuccessful($url)
    {
        $this->getNamePW('admin_test');
        $this->submitLoginForm($this->userName, $this->pw);

        switch ($url) {
            case '/admin':
                $this->expectException(AccessDeniedException::class);
                break;
            default:
        }
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
     * @runInSeparateProcess
     * @dataProvider provideReportUrls
     * @param $url
     */
    public function testReports($url)
    {
        $this->getNamePW('admin_test');
        $this->submitLoginForm($this->userName, $this->pw);

        $this->client->request('GET', $url);
        $this->assertResponseIsSuccessful();

        $rpt = $this->client->getResponse()->headers->all('content-type')[0];
        $this->assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $rpt);
    }

    public function provideReportUrls()
    {
        yield ['/crct'];
        yield ['/ra'];
        yield ['/ri'];
        yield ['/rie'];
        yield ['/ruc'];
        yield ['/urr'];
        yield ['/nra'];
        yield ['/rsh'];
        yield ['/rcdc'];
        yield ['/rss'];
        yield ['/rssx'];
        yield ['/rsca'];
        yield ['/rls'];
        yield ['/rxr'];
        yield ['/newcert'];

    }

    /**
     * @runInSeparateProcess
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
        yield ['/crct'];
        yield ['/ra'];
        yield ['/ri'];
        yield ['/rie'];
        yield ['/ruc'];
        yield ['/urr'];
        yield ['/nra'];
        yield ['/rsh'];
        yield ['/rcdc'];
        yield ['/rss'];
        yield ['/rssx'];
        yield ['/rsca'];
        yield ['/rls'];
        yield ['/rxr'];
        yield ['/newcert'];

//        //unused reports defined in ExportXl
//        yield ['/hrc'];
//        yield ['/nocerts'];

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

    /**
     * @runInSeparateProcess
     */
    public function testReportsAsAnonymous()
    {
        // instantiate the view and test it
        $this->client->request('GET', '/reports');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());
    }

    /**
     * @runInSeparateProcess
     */
    public function testReportsAsUser()
    {
        $this->getNamePW('user_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $crawler = $this->client->followRedirect();

        // verify view
        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

        // verify links & test exports
        $this->verifyLink($crawler, 'Composite Referee Certification & Training', 'crct');
        $this->verifyLink($crawler, 'Referee Instructors', 'ri');
        $this->verifyLink($crawler, 'Referee Instructor Evaluators', 'rie');
        $this->verifyLink($crawler, 'Referee Assessors', 'ra');
        $this->verifyLink($crawler, 'Referee Upgrade Candidates', 'ruc');
        $this->verifyLink($crawler, 'New Referee Certifications', 'newcert');
//        $this->verifyLink($crawler, 'Referees Missing Safe Haven Training', 'rsh');
//        $this->verifyLink($crawler, 'Referees Missing Concussion Training', 'rcdc');
//        $this->verifyLink($crawler, 'Referees Missing Sudden Cardiac Arrest', 'rsca');
//        $this->verifyLink($crawler, 'Referees Missing SafeSport', 'rss');
//        $this->verifyLink($crawler, 'Referees Missing LiveScan', 'rls');
//        $this->verifyLink($crawler, 'Referees with Expired Risk Status', 'rxr');

        $this->assertEmpty($crawler->selectLink('National Referee Assessors')->getNode(0));

    }

    /**
     * @runInSeparateProcess
     */
    public function testReportsAsAdmin()
    {
        $this->getNamePW('admin_test');

        $this->submitLoginForm($this->userName, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $crawler = $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

        // verify Admin links
        $this->verifyLink($crawler, 'National Referee Assessors', 'nra');
    }

}
