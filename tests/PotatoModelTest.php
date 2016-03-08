<?php

namespace Elchroy\ORM\Tests;

use Elchroy\Tests;
use Elchroy\PotatoORM\PotatoModel;
use Elchroy\PotatoORM\Dog;
use \Mockery;

class PotatoModelTest extends \PHPUnit_Framework_TestCase
{
    private $dbMockConn;
    private $dbMockStat;


    public function setUp()
    {
        // $this->dbMockConn = Mockery::mock('Elchroy\PotatoORM\PotatoModel');
        // $this->dbMockConn->shouldReceive('connect');
        // $this->dbMockStat = Mockery::mock('\PDOStatement');
    }


    public function testGetAllFunctionWorks()
    {
        // $sql = "SELECT * FROM dog ";
        // $this->dbMockConn->shouldReceive('prepare')->with($sql)->andReturn($this->dbMockStat);
        // $this->dbMockStat->shouldReceive('execute')->andReturn(true);
        // $getAll = Dog::getAll();
        // $this->assertTrue($getAll);
    }



    protected function teardDown()
    {
    }

    public function testRandomtest()
    {
        $this->assertEquals(9, (14 - 5));
    }

    public function testFindFunctionWorks()
    {
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
