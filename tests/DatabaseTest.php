<?php

use Mockery as m;
use Elchroy\PotatoORM\PotatoModel;

class DatabaseTest extends PHPUnit_Framework_TestCase
{
    private $connection;
    private $query;
    private $connector;
    private $db;
    private $dog;

    public function setUp()
    {
        $this->db = new PDO('sqlite:newDB.sqlite');

        $this->connection = m::mock('PDO', ['sqlite:newDB.sqlite']);
        $this->connector = m::mock('Elchroy\PotatoORM\PotatoConnector');
        $this->connector->shouldReceive('setConnection')->andReturn($this->connection);
        $this->query = m::mock('Elchroy\PotatoORM\PotatoQuery', [$this->connector]);

        //Create a basic users table
        $this->db->exec('CREATE TABLE IF NOT EXISTS dog (id int(25), name varchar (255), price int(10))');
        // echo "Table users has been created <br />";
        //Insert some rows
        $this->db->exec('INSERT INTO dog (id, name, price) VALUES (1, "Bolt", 35000)');
        // echo "Inserted row into table users <br />";
        $this->db->exec('INSERT INTO dog (id, name, price) VALUES (2, "Spyk", 25000)');
        //Insert some rows
        $this->db->exec('INSERT INTO dog (id, name, price) VALUES (3, "Halx", 35500)');
        // echo "Inserted row into table users <br />";
        $this->db->exec('INSERT INTO dog (id, name, price) VALUES (4, "Ferr", 28700)');

        $this->dog = new Dog($this->query);

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
}

class Dog extends PotatoModel
{
}


