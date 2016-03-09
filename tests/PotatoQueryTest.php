<?php

use Elchroy\PotatoORM\PotatoQuery;
use Mockery as m;

class PotatoQueryTest extends \PHPUnit_Framework_TestCase
{
    private $dbMockConn;
    public $mockConnector;
    public $mockStatement;
    private $mockQuery;
    private $mockModel;
    private $dbMockStat;

    public function setUp()
    {
        $this->mockModel = m::mock('Elchroy\PotatoORM\PotatoModel');
        $this->mockConnector = m::mock('Elchroy\PotatoORM\PotatoConnector');
        $this->mockStatement = m::mock('PDOStatement');
        $this->mockQuery = new PotatoQuery($this->mockConnector);
    }

    public function testGetFrom()
    {
        $sql = 'SELECT name, price FROM orders';
        $this->mockConnector->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('execute');
        $this->mockStatement->shouldReceive('fetchAll')->with(\PDO::FETCH_OBJ)->andReturn(new stdClass());

        $result = $this->mockQuery->getFrom('orders', 'name, price');
        $this->assertInstanceOf('stdClass', $result);
    }

    public function testGetOne()
    {
        $sql = 'SELECT * FROM people WHERE id = :id ';
        $this->mockConnector->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('bindParam')->once()->with(':id', 43);
        $this->mockStatement->shouldReceive('execute');
        $this->mockStatement->shouldReceive('fetchObject')->with('people')->andReturn(new stdClass());

        $result = $this->mockQuery->getOne('people', 43);
        $this->assertInstanceOf('stdClass', $result);
    }

    public function testStoreInWorks()
    {
        $data = ['name' => 'Diamane', 'rooms' => 400];
        $sql = 'INSERT INTO hotels (name, rooms) VALUES (?, ?)';
        $this->mockConnector->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('bindParam')->once()->with(1, 'Diamane');
        $this->mockStatement->shouldReceive('bindParam')->once()->with(2, 400);
        $this->mockStatement->shouldReceive('execute')->andReturn(true);

        $result = $this->mockQuery->storeIn('hotels', $data);
        $this->assertEquals(true, $result);
    }

    public function testGetColumnsWorks()
    {
        $data = ['name' => 'Diamane', 'rooms' => 400];
        $columnsString = '(name, rooms)';
        $result = $this->mockQuery->getColumns($data);
        $this->assertEquals($columnsString, $result);
    }

    public function testCountWorks()
    {
        $data = ['name' => 'Diamane', 'rooms' => 400];
        $this->assertEquals(2, count($data));
    }

    public function testDeleteWorks()
    {
        $sql = 'DELETE FROM people WHERE id = :id ';
        $this->mockConnector->shouldReceive('prepare')->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('bindParam')->with(':id', 43);
        $this->mockStatement->shouldReceive('execute')->andReturn(true);

        $result = $this->mockQuery->deleteFrom('people', 43);
        $this->assertEquals(true, $result);
    }

    public function testUpdateFunctionworks()
    {
        $sql = 'UPDATE hotels SET name = :name_val, location = :location_val WHERE id = :id_val';
        $this->mockConnector->shouldReceive('prepare')->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('bindValue')->with(':name_val', 'Diamane');
        $this->mockStatement->shouldReceive('bindValue')->with(':location_val', 'CapeTown, S-Africa');
        $this->mockStatement->shouldReceive('bindValue')->with(':id_val', 32);
        $this->mockStatement->shouldReceive('execute')->andReturn(true);

        $result = $this->mockQuery->updateAt('hotels', ['id' => 32, 'name' => 'Diamane', 'location' => 'CapeTown, S-Africa']);
        $this->assertEquals(true, $result);
    }

    public function testputQuesMarksWorks()
    {
        $result = $this->mockQuery->putQuesMarks(3);
        $this->assertEquals('?, ?, ?', $result);
    }
}
