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
    private $mockConnection;
    private $configFile;
    private $expectedconfig;
    private $adaptar;
    private $host;
    private $dbname;
    private $username;
    private $password;
    private $dsn;

    public function setUp()
    {
        $this->expectedconfig = array(
                "host" => "myhost",
                "username" => "myusername",
                "password" => "",
                "dbname" => "mydb",
                "adaptar" => "mysql",
            );
        $this->adaptar = $this->expectedconfig['adaptar'];
        $this->host = $this->expectedconfig['host'];
        $this->dbname = $this->expectedconfig['dbname'];
        $this->username = $this->expectedconfig['username'];
        $this->password = $this->expectedconfig['password'];
        $this->dsn = "$this->adaptar:host=$this->host;dbname=$this->dbname";

        $this->root =vfsStream::setup('home');
        $this->configFile = vfsStream::url('home/config.ini');

        $this->connector = new PotatoConnector($this->expectedconfig);
        // $this->mockConnection = m::mock('PDO', [$this->dsn, $this->username, $this->password]);
    }

    public function testGetAdaptar()
    {
        $adaptar = $this->connector->getAdaptar();
        $this->assertEquals('mysql', $adaptar);
    }

    public function testGetHost()
    {
        $host = $this->connector->getHost();
        $this->assertEquals('myhost', $host);
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
        $this->assertEquals('', $password   );
    }

    public function testGetConfigurationsIfGivenFilePath()
    {
        $file = fopen($this->configFile, "a");
        $configData = array(
                    "[database]",
                    "host = myhost",
                    "username = myusername",
                    "password = ",
                    "dbname = mydb",
                    "adaptar = mysql",
            );
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

    public function testConnectFunction()
    {
        // $connection = $this->connector->connect('mysql', 'myhost', 'dbname', 'myusername', '');
        // $connection = $this->connector->connect($this->expectedconfig['adaptar'], $this->expectedconfig['host'], $this->expectedconfig['dbname'], $this->expectedconfig['username'], $this->expectedconfig['password']);
        // $this->assertEquals($this->mockConnector, $connection);

    }

    public function testSetConnection()
    {

    }
}