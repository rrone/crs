<?php

namespace Tests\Repository;

use App\Repository\DataWarehouse;
use Doctrine\DBAL\Connection;
use Tests\Abstracts\WebTestCasePlus;

class DataWarehouseTest extends WebTestCasePlus
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var DataWarehouse
     */
    private $dw;

    protected $userName;
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->conn = $this->c->get('doctrine.dbal.default_connection');
        $this->dw = new DataWarehouse($this->conn);

        $this->getNamePW('admin_test');
        $this->user = $this->dw->getUserByName($this->userName);

    }

    protected function tearDown(): void
    {
        $this->conn->close();

    }

    public function testGetUserbyName()
    {
        $result = $this->dw->getUserByName('');

        $this->assertNull($result);
    }

    public function testGetUserbyPW()
    {
        $result = $this->dw->getUserByHash('');
        $this->assertNull($result);

        $result = $this->dw->getUserByHash($this->user->hash);
        $this->assertEquals($this->user, $result);
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
