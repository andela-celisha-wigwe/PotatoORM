<?php

// namespace Elchroy\ORM\Tests;

use Elchroy\PotatoORM\PotatoModel;
use Mockery as m;

class PotatoModelTest extends \PHPUnit_Framework_TestCase
{
    public $mockConnector;
    public $mockStatement;
    private $mockQuery;
    private $mockModel;
    private $model;

    public function setUp()
    {
        $this->mockQuery = m::mock('Elchroy\PotatoORM\PotatoQuery');
        $this->mockModel = new PotatoModel($this->mockQuery);
        // $this->mockModel2 = PotatoModel::find(4);
        $this->mockConnector = m::mock('Elchroy\PotatoORM\PotatoConnector');
        $this->mockStatement = m::mock('PDOStatement');
        // $this->model = new PotatoModel();
    }

    public function teardDown()
    {
        // m::close();
    }

    public function NotestModelHasQueryIfNull()
    {
        $modelQuery = $this->model->queryTo;
        $this->assertInstanceOf('Elchroy\PotatoORM\PotatoQuery', $modelQuery);
    }

    public function testGetMagicFunctionWorksAndREturnTheDataInDatatoSave()
    {
        $model = new PotatoModel($this->mockQuery);
        $model->name = "Puffy";
        $name = $model->name;
        $this->assertContains('name', array_keys($model->dataToSave));
        $this->assertTrue($name == "Puffy");
    }

    public function testGetMagicFunctionDoesNotWorkIfCalledREquesIsNotInDataToSave()
    {
        $model = new PotatoModel($this->mockQuery);
        $name = $model->name;
        $this->assertNotContains('name', $model->dataToSave);
        $this->assertEquals("name not found.", $name);
    }

    public function tesnotMagicFunctionIsSetWorks()
    {
        $this->getMockBuilder('PotatoModel')
        ->setMethods(array('insert'))
        ->getMock();
        $this->expects($this->once())
        ->method('handleValue')
        ->will($this->returnValue(23)); //Whatever value you want to return
    }

    public function nonotestGetAllFunctionWorks()
    {
        // $this->mockModel->shouldReceive('getAll')->andReturn(new stdClass());
        // $result = Elchroy\PotatoORM\PotatoModel::getAll();
        // $this->assertInstanceOf($result, "stdClass");
        // $this->mockModel->shouldReceive('getAll')->once()->andReturn(["name" => "Bolt", "price" => 4567]);
        // $expected = $this->mockModel->getAll();
        // $this->assertEquals($expected, ["name" => "Bolt", "price" => 4567]);
    }

    public function testGetAllFunctionWorksWithNullAsQuery()
    {
    }

    public function testIsStoredFunctionWorksForFalse()
    {
        $model = new PotatoModel($this->mockQuery);
        $result = $model->isStored();
        $this->assertFalse($result);
    }

    public function testIsStoredFunctionWorksForTrue()
    {
        $model = new PotatoModel($this->mockQuery);
        $model->id = 34;
        $result = $model->isStored();
        $this->assertTrue($result);
    }



    public function testModelSave()
    {
    }

    public function testGetClassTableNameWorks()
    {
        // $result = (new Elchroy\PotatoORM\PotatoModel)::getClassTableName();
        // $this->assertEquals("basemodel", $result);
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
        $this->mockQuery->shouldReceive('updateAt')->andReturn(true);

        $this->mockModel->id = 23;
        $this->mockModel->name = 'Harry';
        $result = $this->mockModel->update();

        $this->assertTrue($result);
    }

    public function testInsertFunctionworks()
    {
        $this->mockQuery->shouldReceive('storeIn')->andReturn(true);
        $this->mockModel->name = 'harry';
        $result = $this->mockModel->insert();
        $this->assertTrue($result);
    }

    public function testDestroyFunctionWorks()
    {
    }
}
