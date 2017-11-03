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

    private $mockSR;
    private $projectKey = '2016U16U19Chino';
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

    public function testSetUser()
    {
        $result = $this->mockSR->setUser('');

        $this->assertNull($result);

        //create new user
        $arrUser = (array)$this->user;
        $arrUser['name'] = $this->user->name . 'x';
        $newId = $this->mockSR->setUser($arrUser);
        $this->assertGreaterThan(0, $newId);

        //update new user
        $updateId = $this->mockSR->setUser($arrUser);
        $this->assertEquals($newId, $updateId);

        //remove new user
        $dropId = $this->mockSR->dropUserById('');
        $this->assertNull($dropId);

        $dropId = $this->mockSR->dropUserById($newId);

        $this->assertEquals($newId, $dropId);
    }

    public function testGetEventNull()
    {
        $result = $this->mockSR->getEvent('');

        $this->assertNull($result);
    }

    public function testGetEventLabel()
    {
        $result = $this->mockSR->getEventLabel($this->projectKey);

        $this->assertEquals('16U/19U Playoffs: November 19-20, 2016', $result);
    }

    public function testGetLockedNull()
    {
        $result = $this->mockSR->getLocked($this->projectKey . 'x');

        $this->assertNull($result);
    }

    public function testGetNumberOfRefereesNull()
    {
        $result = $this->mockSR->numberofReferees($this->projectKey . 'x');

        $this->assertNull($result);
    }

    public function testUpdateAssignorNull()
    {
        $result = $this->mockSR->updateAssignor('');

        $this->assertNull($result);
    }

    public function testUpdateAssignmentsNull()
    {
        $result = $this->mockSR->updateAssignments('');

        $this->assertNull($result);
    }

    public function testClearAssignor()
    {
        $games = $this->mockSR->getGamesByRep($this->projectKey, $this->user->name, '%');
        $data = [];

        foreach ($games as $game) {
            $data[$game->id] = $game->assignor;
        }

        $this->assertGreaterThan(0, count($data));

        $this->mockSR->clearAssignor($this->projectKey, $this->user->name);
        $result = $this->mockSR->getGamesByRep($this->projectKey, $this->user->name, '%');

        $this->assertCount(0, $result);
        $this->mockSR->updateAssignor($data);

        $nowGames = $this->mockSR->getGamesByRep($this->projectKey, $this->user->name, '%');

        $this->assertCount(count($data), $nowGames);

    }

    public function testgetNextGameId()
    {
        $id = $this->mockSR->getNextGameId();

        $this->assertGreaterThan(0, $id);
    }

    public function testmodifyGames()
    {
        $result = $this->mockSR->modifyGames(null);

        $this->assertNull($result);
    }

    public function testinsertGame()
    {
        $result = $this->mockSR->insertGame(null);

        $this->assertNull($result);
    }

    public function testgetGame()
    {
        $result = $this->mockSR->getGame(457);

        $this->assertInstanceOf(\stdClass::class, $result);
    }

    public function testupdateGame()
    {
        $result = $this->mockSR->updateGame(null);

        $this->assertNull($result);
    }

    public function testShowVariables()
    {
        $result = $this->mockSR->showVariables(null);

        $this->assertNotEmpty($result);

    }

}