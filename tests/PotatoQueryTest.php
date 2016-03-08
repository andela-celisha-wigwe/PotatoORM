<?php

use Elchroy\PotatoORM\PotatoQuery;
use Mockery as m;
use PDO;

class PotatoQueryTest extends \PHPUnit_Framework_TestCase
{
    private $dbMockConn;
    public $mockConnector;
    public $mockStatement;
    public $mockQuery;
    private $mockModel;
    private $dbMockStat;

    public function setUp()
    {
        $this->mockModel = m::mock('Elchroy\PotatoORM\PotatoModel');
        $this->mockConnector = m::mock('Elchroy\PotatoORM\PotatoConnector');
        $this->mockStatement = m::mock("PDOStatement");
        $this->mockQuery = new PotatoQuery($this->mockConnector);
    }


    public function testGetFrom()
    {
        $sql = "SELECT name, price FROM orders";
        $this->mockConnector->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive("execute");
        $this->mockStatement->shouldReceive("fetchAll")->with(\PDO::FETCH_OBJ)->andReturn(new stdClass);
        $result = $this->mockQuery->getFrom("orders", "name, price");
        $this->assertInstanceOf("stdClass", $result);
    }


    public function testGetOne()
    {
        $sql = "SELECT * FROM people WHERE id = 43 ";
    }
}
