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
        //Open the database mydb
        $this->db = new SQLite3('mydb');
        //Create a basic users table
        $this->db->exec('CREATE TABLE IF NOT EXISTS potatomodel (id int(25), name varchar (255), price int(10))');
        // echo "Table users has been created <br />";
        //Insert some rows
        $this->db->exec('INSERT INTO potatomodel (id, name, price) VALUES (1, "Bolt", 35000)');
        // echo "Inserted row into table users <br />";
        $this->db->exec('INSERT INTO potatomodel (id, name, price) VALUES (2, "Spyk", 25000)');
        //Insert some rows
        $this->db->exec('INSERT INTO potatomodel (id, name, price) VALUES (3, "Halx", 35500)');
        // echo "Inserted row into table users <br />";
        $this->db->exec('INSERT INTO potatomodel (id, name, price) VALUES (4, "Ferr", 28700)');

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
        // $this->mockConnection = m::mock('PDO', [$this->dsn, $this->username, $this->password]);
        $this->mockConnection = m::mock('PDO', ['sqlite:mydb.sqlite']);

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
    public function testSetConnectionFunctionThrowsException()
    {
        $connection = $this->connector->connect('wrongAdaptar', 'wrongHostname', 'wrongDbName', 'wrongUsername', 'wrongPassword');
        $this->assertInstanceOf('PDO', $connection);
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
