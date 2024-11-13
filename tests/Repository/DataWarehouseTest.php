<?php

namespace Tests\Repository;

use App\Repository\DataWarehouse;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataWarehouseTest extends KernelTestCase
{
    protected static $container;

    /**
     * @var Connection|object|null
     */
    protected static $conn;

    /**
     * @var DataWarehouse
     */
    protected DataWarehouse $dw;

    protected string $userName;
    protected stdClass $user;
    protected ContainerInterface $c;
    protected string $pw;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->c = self::$kernel->getContainer();

        self::$conn = $this->c->get('doctrine.dbal.default_connection');
        $this->dw = new DataWarehouse(self::$conn);

        $this->getNamePW('admin_test');
        $this->user = $this->dw->getUserByName($this->userName);

    }

    protected function tearDown(): void
    {
        self::$conn->close();

    }

    protected function getNamePW($paramStr = null)
    {
        if (empty($paramStr)) {
            $this->userName = '';
            $this->pw = '';
        }

        $cred = self::$container->getParameter($paramStr);

        $this->userName = $cred['user'];
        $this->pw = $cred['pw'];

    }
    public function testGetUserByName()
    {
        $result = $this->dw->getUserByName('');

        $this->assertNull($result);
    }

    public function testGetUserByPW()
    {
        $result = $this->dw->getUserByHash('');
        $this->assertNull($result);

        $result = $this->dw->getUserByHash($this->user->hash);
        $this->assertEquals($this->user, $result);
    }

    public function testSetUser()
    {
        $result = $this->dw->setUser('');
        $this->assertNull($result);

        $result = $this->dw->SetUser(json_decode(json_encode($this->user), true));
        $this->assertIsInt((int) $result);
    }

    public function testRemoveUser()
    {
        $result = $this->dw->removeUser('');
        $this->assertNull($result);

    }

    public function testDBMethods()
    {
        $result = $this->dw->getRefAssessors('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefNationalAssessors();
        $this->assertEquals(42, sizeof($result));

        $result = $this->dw->getRefInstructors('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefInstructorEvaluators('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefUpgradeCandidates('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getSafeHavenRefs('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getConcussionRefs('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getSafeSportExpirationRefs('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getLiveScanRefs('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getExpiredRiskRefs('1', 10);
        $this->assertEquals(10, sizeof($result));

//        $result = $this->dw->getRefsNewCerts('1', 5);
//        $this->assertEquals(5, sizeof($result));

    }
}
