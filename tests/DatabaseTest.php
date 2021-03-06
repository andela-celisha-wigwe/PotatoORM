<?php

use Elchroy\PotatoORM\PotatoModel;
use Mockery as m;

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    private $connection;
    private $query;
    private $connector;
    private $db;

    public function setUp()
    {
        $this->connector = m::mock('Elchroy\PotatoORM\PotatoConnector');
        $this->connector->shouldReceive('setConnection')->andReturn($this->connection);
        $this->query = m::mock('Elchroy\PotatoORM\PotatoQuery', [$this->connector]);
    }

    public function testGetAllAsStatic()
    {
        $this->query->connection = $this->db;
        $this->query->shouldReceive('getFrom');
        $dogs = Dog::getAll($this->query);
    }

    public function testFindIdAsStatic()
    {
        $this->query->connection = $this->db;
        $this->query->shouldReceive('getOne')->with('dog', 3);
        $dog = Dog::find(3, $this->query);
    }

    public function testDeleteIdAsStatic()
    {
        $this->query->connection = $this->db;
        $this->query->shouldReceive('deleteFrom')->with('dog', 1);
        $this->query->shouldReceive('getFrom');
        $dog = Dog::destroy(1, $this->query);
    }

    public function testDataBasecolumn()
    {
    }
}

class Dog extends PotatoModel
{
}
