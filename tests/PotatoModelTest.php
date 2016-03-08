<?php

// namespace Elchroy\ORM\Tests;

use Elchroy\PotatoORM\PotatoQuery;
use Mockery as m;

class PotatoModelTest extends \PHPUnit_Framework_TestCase
{
    private $dbMockConn;
    public $mockConnector;
    public $mockStatement;
    public $mockQuery;
    private $mockModel;
    private $dbMockStat;

    public function setUp()
    {
        // $this->mockModel = m::mock('Elchroy\PotatoORM\PotatoModel');
        // $this->mockConnector = m::mock('Elchroy\PotatoORM\PotatoConnector');
        // $this->mockStatement = m::mock("PDOStatement");
        // $this->mockQuery = new PotatoQuery($this->mockConnector);
    }

    public function teardDown()
    {
        // m::close();
    }


    // $this->assertEquals($result, );

    // $this->mockModel->shouldReceive('getAll')->once()->andReturn(["name" => "Bolt", "price" => 4567]);
    // $expected = $this->mockModel->getAll();
    // $this->assertEquals($expected, ["name" => "Bolt", "price" => 4567]);

    public function testGetAllFunctionWorks()
    {
        // $this->mockModel->shouldReceive('getAll')->once()->andReturn(["name" => "Bolt", "price" => 4567]);
        // $expected = $this->mockModel->getAll();
        // $this->assertEquals($expected, ["name" => "Bolt", "price" => 4567]);
    }

    public function testFindFunctionWorks()
    {
        // $this->mockModel->shouldReceive('find')->with()->once()->andReturn(["name" => "Bolt", "price" => 456]);
        // $this->mockModel->find(13);
        // $this->assertEquals($expected, ["name" => "Bolt", "price" => 4567]);
    }

    public function testSavefunctionWorks()
    {
    }

    public function testUpdateFunctionworks()
    {
    }

    public function testDestroyFunctionWorks()
    {
    }
}
