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
    private Connection $conn;

    /**
     * @var DataWarehouse
     */
    private DataWarehouse $dw;

    protected string $userName;
    private object $user;

    protected function setUp() : void
    {
        parent::setUp();

        $this->conn = $this->c->get('doctrine.dbal.default_connection');
        $this->dw = new DataWarehouse($this->conn);

        $this->getNamePW('admin_test');
        $this->user = $this->dw->getUserByName($this->userName);

    }

    protected function tearDown() : void
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

}
