<?php

use Elchroy\PotatoORM\PotatoConnector;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class PotatoConnectorTest extends PHPUnit_Framework_TestCase
{
    private $root;
    private $connector;
    private $mockConnection;
    private $configFile;
    private $expectedconfig;
    private $adaptar;
    private $host;
    private $dbname;
    private $username;
    private $password;

    public function setUp()
    {
        $this->expectedconfig = [
                'host'     => 'myhost',
                'username' => 'myusername',
                'password' => '',
                'dbname'   => 'mydb',
                'adaptar'  => 'sqlite',
                'sqlite_file' => 'sample.db'
            ];
        $this->adaptar = $this->expectedconfig['adaptar'];
        $this->host = $this->expectedconfig['host'];
        $this->dbname = $this->expectedconfig['dbname'];
        $this->username = $this->expectedconfig['username'];
        $this->password = $this->expectedconfig['password'];

        $this->root = vfsStream::setup('home');
        $this->configFile = vfsStream::url('home/config.ini');

        $this->connector = new PotatoConnector($this->expectedconfig);
    }

    public function testGetAdaptar()
    {
        $adaptar = $this->connector->getAdaptar();
        $this->assertEquals('sqlite', $adaptar);
    }

    public function testGetHost()
    {
        $host = $this->connector->getHost();
        $this->assertEquals('myhost', $host);
    }

    public function testSqliteConnect()
    {
        $result = $this->connector->sqliteConnect('sqlite', 'sample.db');
        $this->assertInstanceOf('PDO', $result);
    }

    public function testConnectDriverForSQLite()
    {
        $result = $this->connector->connectDriver('sqlite', $this->host, $this->dbname, $this->username, $this->password);
        $this->assertInstanceOf('PDO', $result);
    }

    public function testConnectDriverForMySql()
    {
        $conn = mysqli_connect('127.0.0.1', 'root', '');
        mysqli_query($conn, 'CREATE DATABASE IF NOT EXISTS elchroy');
        $result = $this->connector->connectDriver('mysql', '127.0.0.1', 'elchroy', 'root', '');
        $this->assertInstanceOf('PDO', $result);
        mysqli_query($conn, 'DROP DATABASE elchroy'); //Destroy the database;
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\InvalidAdaptarException
     *
     * @expectedExceptionMessage Invalid Adapter wrongAdaptar : Please provide a driver for the connection to the database.
     */
    public function testConnectDriverforException()
    {
        $result = $this->connector->connectDriver('wrongAdaptar', 'host', 'dbname', 'username', 'password');
    }

    public function testGetUsername()
    {
        $username = $this->connector->getUsername();
        $this->assertEquals('myusername', $username);
    }

    public function testGetDBName()
    {
        $dbname = $this->connector->getDBName();
        $this->assertEquals('mydb', $dbname);
    }

    public function testGetPassWord()
    {
        $password = $this->connector->getPassword();
        $this->assertEquals('', $password);
    }

    public function testGetConfigurationsIfGivenFilePath()
    {
        $file = fopen($this->configFile, 'a');
        $configData = [
                    '[database]',
                    'host = myhost',
                    'username = myusername',
                    'password = ',
                    'dbname = mydb',
                    'adaptar = sqlite',
                    'sqlite_file = sample.db'
            ];
        foreach ($configData as $cfg) {
            fwrite($file, $cfg."\n");
        }
        fclose($file);
        $result = $this->connector->getConfigurations($this->configFile);
        $this->assertEquals($this->expectedconfig, $result);
    }

    public function testGetConFilePath()
    {
        $path = $this->connector->getConfigFilePath($this->configFile);
        $this->assertEquals('vfs://home/config.ini', $path);
    }

    public function testSetConnectionFunction()
    {
        $connection = $this->connector->connect($this->adaptar, $this->host, $this->dbname, $this->username, $this->password);
        $this->assertInstanceOf('PDO', $connection);
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyConnectionException
     *
     * @expectedExceptionMessage Please provide a driver for the connection to the database.
     */
    public function testSetConnectionFunctionThrowsException()
    {
        $connection = $this->connector->connect('wrongAdaptar', 'wrongHostname', 'wrongDbName', 'wrongUsername', 'wrongPassword');
    }

    public function testSetConnection()
    {
        $connection = $this->connector->setConnection();
        $this->assertInstanceOf('PDO', $connection);
    }

    public function tearDown()
    {
        m::close();
    }
}
