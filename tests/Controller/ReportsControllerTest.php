<?php
namespace Tests\Controller;

use App\Controller\ReportsController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Tests\Abstracts\WebTestCasePlus;

class ReportsControllerTest extends WebTestCasePlus
{
    /**
     * @dataProvider provideHomeUrls
     */
    public function testLogonSuccessful($url)
    {
        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function provideHomeUrls()
    {
        yield ['/'];
        yield ['/logon'];
    }

    /**
     * @dataProvider providePageUrls
     */
    public function testPageIsSuccessful($url)
    {
        $this->getNamePW('admin_test');

        $this->login($this->user, $this->pw);

        $this->client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function providePageUrls()
    {
        yield ['/reports'];
        yield ['/bshca'];
        yield ['/ra'];
        yield ['/ri'];
        yield ['/rie'];
        yield ['/ruc'];
//        yield ['/urr'];
        yield ['/nra'];

    }

    /**
     * @dataProvider provideRedirectUrls
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

    }

    public function testReportsAsAnonymous()
    {
        // instantiate the controller
        $rs = new RequestStack();
        $controller = new ReportsController($rs);
        $this->assertTrue($controller instanceof AbstractController);

        // instantiate the view and test it
        $this->client->request('GET', '/reports');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $this->crawler = $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/', $this->client->getRequest()->getPathInfo());

    }

    public function xtestReportsAsUser()
    {
        $this->getNamePW('user_test');

        $this->login($this->user, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->crawler = $this->client->followRedirect();

        // verify view
        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

        // verify links & test exports
//        $this->linkYieldsReport('Composite Referee Certifications (Highest Certification, Safe Haven & Concussion Awareness)', 'bshca');
        $this->linkYieldsReport('Referee Assessors', 'ra');
        $this->linkYieldsReport('Referee Instructors', 'ri');
        $this->linkYieldsReport('Referee Instructor Evaluators', 'rie');
        $this->linkYieldsReport('Referee Upgrade Candidates', 'ruc');
        $this->linkYieldsReport('Unregistered Referees', 'urr');

        $this->assertEmpty( $this->crawler->selectLink('National Referee Assessors')->getNode(0));

    }

    public function xtestReportsAsAdmin()
    {
        $this->getNamePW('admin_test');

        $this->login($this->user, $this->pw);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->crawler = $this->client->followRedirect();

        $view = $this->client->getResponse()->getContent();
        $this->assertStringContainsString("<h3>Notes on these reports:</h3>", $view);

        // verify links
//        $this->linkYieldsReport('Composite Referee Certifications (Highest Certification, Safe Haven & Concussion Awareness)', 'bshca');
        $this->linkYieldsReport('Referee Assessors', 'ra');
        $this->linkYieldsReport('Referee Instructors', 'ri');
        $this->linkYieldsReport('Referee Instructor Evaluators', 'rie');
        $this->linkYieldsReport('Referee Upgrade Candidates', 'ruc');
        $this->linkYieldsReport('Unregistered Referees', 'urr');
        $this->linkYieldsReport('National Referee Assessors', 'nra');

    }

}
