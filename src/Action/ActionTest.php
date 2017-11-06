<?php
namespace Tests;

use App\Action\DataWarehouse;
use Slim\Container;

class ActionTest extends AppTestCase
{
    /**
     * @var Container
     */
    protected $c;

    /**
     * @var DataWarehouse
     */
    private $mockSR;

    private $userName;
    private $user;

    public function setUp()
    {
        $this->app = $this->getSlimInstance();
        $this->c = $this->app->getContainer();
        $db = $this->c->get('db');

        $this->mockSR = new DataWarehouse($db);

        $this->userName = $this->config['user_test']['user'];
        $this->user = $this->mockSR->getUserByName($this->userName);

    }

    public function testGetUserbyName()
    {
        $result = $this->mockSR->getUserByName('');

        $this->assertNull($result);
    }

    public function testGetUserbyPW()
    {
        $result = $this->mockSR->getUserByPW('');
        $this->assertNull($result);

        $result = $this->mockSR->getUserByPW($this->user->hash);
        $this->assertEquals($this->user, $result);
    }

}