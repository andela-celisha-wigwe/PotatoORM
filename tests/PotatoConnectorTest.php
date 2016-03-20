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
    private $testClass;
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
        $this->testClass = vfsStream::url('home/Book.php');
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
        // Create the Database using mysqli.
        // mysqli_query("CREATE DATABASE dbname", );
        $mockPDOSqlite = m::mock('PDO', ["mysql:host=localhost;dbname=dbname", "root", ""]);
        $result = $this->connector->connectDriver('mysql', "localhost", "orm", "root", "");
        $this->assertInstanceOf('PDO', $result);
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

    public function notestSqliteConnectLocatesFile()
    {
        //Try using SQLite to write to the mock file. Not easy though.
        $result = $this->connector->getSqliteFile();
        $this->assertEquals('/Users/user/Code/learn/checkpoint2/potatoorm/src/../db.sqlite', $result);
    }

    public function notestDB()
    {
        $dbFileMock = fopen($this->sqliteFile, "w");
        $dbContents = file_get_contents("testDB.sqlite");
        fwrite($dbFileMock, $dbContents);
        fclose($dbFileMock);

        $classFile = fopen($this->testClass, "w");
        $classContents = "<?php class Dog extends Elchroy\PotatoORM\PotatoModel { } ?>";
        fwrite($classFile, $classContents);
        fclose($classFile);

        $db = fopen("testDB.sqlite", "r");
        var_dump($db);

        $sql = "SHOW TABLES";
        // $nc = new PDO("sqlite:".$db);
        $nc = new PDO("sqlite:testDB.sqlite");
        $rs = $nc->exec($sql);
        var_dump($rs);
        $fstmt = m::mock('PDOStatement');
        $fconnection = m::mock('PDO', ["sqlite:$db"]);
        $fconnection->shouldReceive('prepare')->with($sql)->andReturn($fstmt);
        $fstmt = $fconnection->prepare($sql);
        $fstmt->shouldReceive('execute');
        $fstmt->shouldReceive('fetchObject')->andReturn(new stdClass);
        $fstmt->execute();
        $result = $fstmt->fetchObject();
        // var_dump($result);
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
