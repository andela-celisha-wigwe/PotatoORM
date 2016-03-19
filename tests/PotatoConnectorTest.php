<?php

use Elchroy\PotatoORM\PotatoConnector;
use Elchroy\PotatoORM\PotatoModel;
use Mockery as m;
use org\bovigo\vfs\vfsStream;

class PotatoConnectorTest extends PHPUnit_Framework_TestCase
{
    private $root;
    private $connector;
    private $mockConnection;
    private $configFile;
    private $sqliteFile;
    private $expectedconfig;
    private $adaptar;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $dsn;
    private $db;

    public function setUp()
    {
        $this->expectedconfig = [
                'host'     => 'myhost',
                'username' => 'myusername',
                'password' => '',
                'dbname'   => 'mydb',
                'adaptar'  => 'sqlite',
            ];
        $this->adaptar = $this->expectedconfig['adaptar'];
        $this->host = $this->expectedconfig['host'];
        $this->dbname = $this->expectedconfig['dbname'];
        $this->username = $this->expectedconfig['username'];
        $this->password = $this->expectedconfig['password'];
        $this->dsn = "$this->adaptar:host=$this->host;dbname=$this->dbname";

        $this->root = vfsStream::setup('home');
        $this->configFile = vfsStream::url('home/config.ini');
        $this->sqliteFile = vfsStream::url('home/db.sqlite');
        $this->mockConnection = m::mock('PDO', [$this->dsn, $this->username, $this->password]);

        $this->connector = new PotatoConnector($this->expectedconfig);
        // $this->mockConnection = m::mock('PDO');
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
        $result = $this->connector->sqliteConnect('sqlite', __DIR__.'/../db.sqlite');
        $this->assertInstanceOf('PDO', $result);
    }

    public function testConnectDriverForSQLite()
    {
        $result = $this->connector->connectDriver('sqlite', $this->host, $this->dbname, $this->username, $this->password);
        $this->assertInstanceOf('PDO', $result);

    }

    public function notestConnectDriverForMySql()
    {
        $result = $this->connector->connectDriver('mysql', $this->host, $this->dbname, $this->username, $this->password);
        die('here');
        $this->assertInstanceOf('PDO', $this->mockConnection);
    }

    /**
     * @expectedException Elchroy\PotatoORMExceptions\FaultyConnectionException
     *
     * @expectedExceptionMessage could not find driver
     */
    public function notestConnectDriverforException()
    {
        $result = $this->connector->connectDriver('wrongAdaptar', 'host', 'dbname', 'username', 'password');
    }

    public function testSqliteConnectLocatesFile()
    {
        //Try using SQLite to write to the mock file. Not easy though.
        $result = $this->connector->getSqliteFile();
        $this->assertEquals('/Users/user/Code/learn/checkpoint2/potatoorm/src/../db.sqlite', $result);
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
            ];
        foreach ($configData as $cfg) {
            fwrite($file, $cfg."\n");
        }
        fclose($file);
        $result = $this->connector->getConfigurations($this->configFile);
        $this->assertEquals($this->expectedconfig, $result);
    }

    public function testGetConfigurationsIfNotGivenFilePath()
    {
        // $connector = new PotatoConnector();
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
     * @expectedExceptionMessage could not find driver
     */
    public function notestSetConnectionFunctionThrowsException()
    {
        $connection = $this->connector->connect('wrongAdaptar', 'wrongHostname', 'wrongDbName', 'wrongUsername', 'wrongPassword');
        // $this->assertInstanceOf('PDO', $connection);
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
