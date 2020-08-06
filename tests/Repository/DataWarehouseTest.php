<?php

namespace Tests\Repository;

use App\Repository\DataWarehouse;
use Doctrine\DBAL\Connection;
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
    protected $dw;

    protected $userName;
    protected $user;
    protected $c;
    protected $pw;

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

    public function testUnusedDBMethods()
    {
        $result = $this->dw->getHighestRefCerts('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefAssessors('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefNationalAssessors('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefInstructors('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefInstructorEvaluators('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefUpgradeCandidates('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getUnregisteredRefs('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getSafeHavenRefs('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefsConcussion('1', 10);
        $this->assertEquals(10, sizeof($result));

        $result = $this->dw->getRefsWithNoBSCerts('1', 10);
        $this->assertEquals(0, sizeof($result));
    }
}
