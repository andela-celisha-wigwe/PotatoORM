<?php

use Elchroy\PotatoORM\PotatoQuery;
use Mockery as m;

class PotatoQueryTest extends \PHPUnit_Framework_TestCase
{
    public $mockConnector;
    public $mockStatement;
    private $mockQuery;

    public function setUp()
    {
        $this->mockConnector = m::mock('Elchroy\PotatoORM\PotatoConnector');
        $this->mockConnection = m::mock('PDO');
        $this->mockConnector->shouldReceive('setConnection')->andReturn($this->mockConnection);
        $this->mockStatement = m::mock('PDOStatement');
        $this->mockQuery = new PotatoQuery($this->mockConnector);
    }

    public function testGetFrom()
    {
        $sql = 'SELECT name, price FROM orders';
        $this->mockConnection->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        // $this->mockConnector->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('execute');
        $this->mockStatement->shouldReceive('fetchAll')->with(\PDO::FETCH_CLASS)->andReturn(new stdClass());
        // $this->mockStatement->shouldReceive('fetchObject')->with('orders')->andReturn(true);

        $result = $this->mockQuery->getFrom('orders', 'name, price');
        $this->assertInstanceOf('stdClass', $result);
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyExecutionException
     *
     * @expectedExceptionMessage There was a problem with excecuting this query.
     */
    public function testGetFromThrowsExceptionForWrongExecution()
    {
        $sql = 'SELECT name, price FROM orders';
        $this->mockConnection->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('execute')->andThrow('Elchroy\PotatoORMExceptions\FaultyExecutionException', 'There was a problem with excecuting this query.');
        $result = $this->mockQuery->getFrom('orders', 'name, price');
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyOrNoTableException
     *
     * @expectedExceptionMessage There seems to be a problem. Please confirm if the 'people' table exists in the database.
     */
    public function testGetFromThrowsException()
    {
        $sql = 'SELECT name, price FROM people';
        $this->mockConnection->shouldReceive('prepare')->once()->with($sql)->andReturn(false);
        $result = $this->mockQuery->getFrom('people', 'name, price');
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyOrNoTableException
     *
     * @expectedExceptionMessage There seems to be a problem. Please confirm if the 'people' table exists in the database.
     */
    public function testGetOneThrowsException()
    {
        $sql = 'SELECT * FROM people WHERE id = :id ';
        $this->mockConnection->shouldReceive('prepare')->once()->with($sql)->andReturn(false);

        $result = $this->mockQuery->getOne('people', 43);
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyExecutionException
     *
     * @expectedExceptionMessage There seems to be a problem. Please confirm if the 'people' table exists in the database.
     */
    public function teddstTryExceutingThrowsException()
    {
    }

    public function testGetOne()
    {
        $sql = 'SELECT * FROM people WHERE id = :id ';
        $this->mockConnection->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
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
        $this->mockConnection->shouldReceive('prepare')->once()->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('bindParam')->once()->with(1, 'Diamane');
        $this->mockStatement->shouldReceive('bindParam')->once()->with(2, 400);
        $this->mockStatement->shouldReceive('execute')->andReturn(true);

        $result = $this->mockQuery->storeIn('hotels', $data);
        $this->assertEquals(true, $result);
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyOrNoTableException
     *
     * @expectedExceptionMessage There seems to be a problem. Please confirm if the 'people' table exists in the database.
     */
    public function testStoreInThrowsException()
    {
        $data = ['name' => 'Diamane', 'rooms' => 400];
        $sql = 'INSERT INTO people (name, rooms) VALUES (?, ?)';
        $this->mockConnection->shouldReceive('prepare')->once()->with($sql)->andReturn(false);

        $result = $this->mockQuery->storeIn('people', $data);
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
        $this->mockConnection->shouldReceive('prepare')->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('bindParam')->with(':id', 43);
        $this->mockStatement->shouldReceive('execute')->andReturn(true);

        $result = $this->mockQuery->deleteFrom('people', 43);
        $this->assertEquals(true, $result);
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyOrNoTableException
     *
     * @expectedExceptionMessage There seems to be a problem. Please confirm if the 'people' table exists in the database.
     */
    public function testDeleteThrowsException()
    {
        $sql = 'DELETE FROM people WHERE id = :id ';
        $this->mockConnection->shouldReceive('prepare')->with($sql)->andReturn(false);

        $result = $this->mockQuery->deleteFrom('people', 43);
    }

    public function testUpdateFunctionworks()
    {
        $sql = 'UPDATE hotels SET name = :name_val, location = :location_val WHERE id = :id_val';
        $this->mockConnection->shouldReceive('prepare')->with($sql)->andReturn($this->mockStatement);
        $this->mockStatement->shouldReceive('bindValue')->with(':name_val', 'Diamane');
        $this->mockStatement->shouldReceive('bindValue')->with(':location_val', 'CapeTown, S-Africa');
        $this->mockStatement->shouldReceive('bindValue')->with(':id_val', 32);
        $this->mockStatement->shouldReceive('execute')->andReturn(true);

        $result = $this->mockQuery->updateAt('hotels', ['id' => 32, 'name' => 'Diamane', 'location' => 'CapeTown, S-Africa']);
        $this->assertEquals(true, $result);
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyOrNoTableException
     *
     * @expectedExceptionMessage There seems to be a problem. Please confirm if the 'people' table exists in the database.
     */
    public function testUpdateFunctionThrowsException()
    {
        $sql = 'UPDATE people SET name = :name_val, location = :location_val WHERE id = :id_val';
        $this->mockConnection->shouldReceive('prepare')->with($sql)->andReturn(false);

        $result = $this->mockQuery->updateAt('people', ['id' => 32, 'name' => 'Diamane', 'location' => 'CapeTown, S-Africa']);
    }

    public function testputQuesMarksWorks()
    {
        $result = $this->mockQuery->putQuesMarks(3);
        $this->assertEquals('?, ?, ?', $result);
    }
}
