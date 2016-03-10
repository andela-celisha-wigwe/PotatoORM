<?php

use Elchroy\PotatoORM\PotatoModel;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use Elchroy\PotatoORM\PotatoConnector;


class PotatoConnectorTest extends PHPUnit_Framework_TestCase
{

    private $root;
    private $connector;
    private $mockConnector;
    public function setUp()
    {
        $this->connector = new PotatoConnector();
        $this->mockConnector = m::mock('PotatoConnector');
        $this->root =vfsStream::setup('home');
        $configFile = vfsStream::url('home/config.ini');
        $db = "[database]";
        $host = "host = myhost";
        $username = "username = myusername";
        $password = "password =";
        $dbname = "dbname = mydb";
        $adaptar = "adaptar = mysql";
        file_put_contents($configFile, $db);
        file_put_contents($configFile, $host);
        file_put_contents($configFile, $username);
        file_put_contents($configFile, $password);
        file_put_contents($configFile, $dbname);
        file_put_contents($configFile, $adaptar);
    }

    public function testGetConfigurationWorks()
    {
        $config = array(
                "host" => "myhost",
                "username" => "myusername",
                "password" => "",
                "dbname" => "mydb",
                "adaptar" => "mysql",
            );
        $this->mockConnector->shouldReceive('getConfigurations')->andReturn($config);
        $result = $this->mockConnector->getConfigurations();
        $this->assertTrue($config == $result);
    }

    public function testGetAdaptar()
    {
        $this->mockConnector->shouldReceive('getAdaptar')->andReturn('mysql');

        $adaptar = $this->mockConnector->getAdaptar();
        $this->assertEquals('mysql', $adaptar);
    }

    public function testGetHost()
    {
        $this->mockConnector->shouldReceive('getHost')->andReturn('myhost');

        $host = $this->mockConnector->getHost();
        $this->assertEquals('myhost', $host);
    }

    public function testGetUsername()
    {
        $this->mockConnector->shouldReceive('getUsername')->andReturn('myusername');

        $username = $this->mockConnector->getUsername();
        $this->assertEquals('myusername', $username);
    }


    public function testGetDBName()
    {
        $this->mockConnector->shouldReceive('getDBName')->andReturn('mysql');

        $dbname = $this->mockConnector->getDBName();
        $this->assertEquals('mysql', $dbname);
    }


    public function testGetPassWord()
    {
        $this->mockConnector->shouldReceive('getPassword')->andReturn('');

        $password = $this->mockConnector->getPassword();
        $this->assertEquals('', $password   );
    }






    public function tekjhvbkstDirectoryIsCreated()
    {
        $connection = new PotatoConnector();
        $this->assertFalse(vfsStreamWrapper::getRoot()->hasChild('id'));

        $connection->getConfigurations(vfsStream::url('dbDirectory'));
        $this->assertTrue(vfsStreamWrapper::getRoot()->hasChild('configs'));
    }
}