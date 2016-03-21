<?php

namespace Elchroy\PotatoORM;

use Elchroy\PotatoORMExceptions\FaultyConnectionException;
use Elchroy\PotatoORMExceptions\InvalidAdaptarException;
use PDO;
use PDOException;

class PotatoConnector
{
    /**
     * [$connection PDO connection to be used to communicate with the database].
     *
     * @var [type] PDO Connection
     */
    public $connection;

    /**
     * [$configuration The configuration data to be used to establish the connection].
     *
     * @var [type]
     */
    public $configuration;

    /**
     * [__construct Set up the connection with the configuration data that is provided on instantiation of the class.].
     *
     * @param array|null $configData [description]
     */
    public function __construct(array $configData = null)
    {
        $configData == null ? $config = $this->getConfigurations() : $config = $configData;
        $this->configuration = $config;
        $this->connection = $this->setConnection();
    }

    /**
     * [setConnection Set up the connection with the configuration information].
     */
    public function setConnection()
    {
        $adaptar = $this->getAdaptar();
        $host = $this->getHost();
        $dbname = $this->getDBName();
        $username = $this->getUsername();
        $password = $this->getPassword();
        $connection = $this->connect($adaptar, $host, $dbname, $username, $password);

        return $connection;
    }

    /**
     * [connect Try setting up the connection wtith given connection parameters].
     *
     * @param [string] $adaptar  [The adapter to be used witht he connection to the database]
     * @param [string] $host     [The host name for the connection]
     * @param [string] $dbname   [The name of the database]
     * @param [string] $username [The username to be used if it is required]
     * @param [string] $password [The [assword to be used for the connectionif required.]]
     *
     * @return [type] [A PDO connection to the databsase]
     */
    public function connect($adaptar, $host, $dbname, $username, $password)
    {
        try {
            $connection = $this->connectDriver($adaptar, $host, $dbname, $username, $password);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $this->throwFaultyConnectionException($message);
        }

        return $connection;
    }

    /**
     * [connectDriver Check which driver is chosen and create a PDO connection based on the driver information.]
     * Throw an InvalidAdaptarException if the given driver is invalid, does not exist or is not compatible with PDO.
     * @param  [string] $adaptar  [The adaptar/driver name used to create PDO connection.]
     * @param  [string] $host     [The hostname to be used for the PDO connection if mysql is chosen.]
     * @param  [string] $dbname   [The name of the database of the PDO connection.]
     * @param  [string] $username [The username to be used for the PDO connection if mysql is chosen.]
     * @param  [string] $password [The password to be used for the PDO connection id mysql is chosen.]
     * @return [type]           [description]
     */
    public function connectDriver($adaptar, $host, $dbname, $username, $password)
    {
        switch ($adaptar) {
            case 'sqlite':
                $connection = $this->sqliteConnect($adaptar);
                break;
            case 'mysql':
                $connection = $this->mysqlConnect($adaptar, $host, $dbname, $username, $password);
                break;
            default:
                $this->throwInvalidAdapterException($adaptar);
                break;
        }
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    /**
     * [mysqlConnect Create a MySQL PDO connection with the mysql driver.]
     * @param  [string] $adaptar  [The adaptar name used to create PDO connection.]
     * @param  [string] $host     [The hostname to be used for the PDO connection.]
     * @param  [string] $dbname   [The name of the database of the PDO connection.]
     * @param  [string] $username [The username to be used for the PDO connection.]
     * @param  [string] $password [The password to be used for the PDO connection.]
     * @return [type]           A PDO connection done with a mysql driver
     */
    public function mysqlConnect($adaptar, $host, $dbname, $username, $password)
    {
        $connection = new PDO("$adaptar:host=$host;dbname=$dbname", $username, $password);
        return $connection;
    }

    /**
     * [sqliteConnect Create an SQLite connection if the selected PDO drver is sqlite.]
     * @param  [string] $adaptar The sqlite driver name
     * @param  [type] $dbFile  [The database file. If this is null, then get the database file using the getSqliteFile method.]
     * @return [type]          A PDO connection done with am sqlite driver.
     */
    public function sqliteConnect($adaptar, $dbFile = null)
    {
        $dbFile = $dbFile == null ? $this->getSqliteFile() : $dbFile;
        $connection = new PDO("$adaptar:$dbFile");
        return $connection;
    }

    /**
     * [getSqliteFile Get the file location of the slqlite file if it is preferred to use an sqlite pdo connection.]
     * @param  [type] $path [The path to the sqlite database file.]
     *
     * @return [type]       [The path to the sqlite file if provided or a default file path if the path in the provided argument is null.]
     */
    public function getSqliteFile($path = null)
    {
        return $path = $path == null ? __DIR__.'/../db.sqlite' : $path;
    }

    /**
     * [getConfigurations Get the configuration data from the file path].
     *
     * @param [type] $filepath [The file path where the connection configuration information are located.]
     *
     * @return [array] [An array of the configuration information after parsing the information.]
     */
    public function getConfigurations($filepath = null)
    {
        $file = ($filepath == null ? $this->getConfigFilePath() : $filepath);

        return parse_ini_file($file);
    }

    /**
     * [getConfigFilePath Get the file path of the file where the configuration lies.].
     *
     * @return [string] [The file path of the location where the confuiguration information lie.]
     */
    public function getConfigFilePath($path = null)
    {
        return $path == null ? __DIR__."/../config.ini" : $path;
    }

    /**
     * [getAdaptar Get the name of the adapter to be used for the connection.].
     *
     * @return [string] [The name of the adapter.]
     */
    public function getAdaptar()
    {
        return $this->configuration['adaptar'];
    }

    /**
     * [getHost Get the name of the host to be used for the connection.].
     *
     * @return [string] [The name of the host.]
     */
    public function getHost()
    {
        return $this->configuration['host'];
    }

    /**
     * [getDBName Get the name of the database where information/data will eb stored.].
     *
     * @return [string] [The name of the database.]
     */
    public function getDBName()
    {
        return $this->configuration['dbname'];
    }

    /**
     * [getUsername Get the username for the connection to the database.].
     *
     * @return [string] [The username for the connection to the database.]
     */
    public function getUsername()
    {
        return $this->configuration['username'];
    }

    /**
     * [getPassword Get the password of the user of the connection.].
     *
     * @return [string] [The passwird of the user.]
     */
    public function getPassword()
    {
        return $this->configuration['password'];
    }

    /**
     * [throwFaultyConnectionException Throw an exception if along the lime the connection is not setup correctly].
     *
     * @param [string] $message [The message to be related in event of this exception.]
     *
     * @return [type] [An inherited PDO exception with a customized message.]
     */
    public function throwFaultyConnectionException($message)
    {
        throw new FaultyConnectionException($message);
    }

    /**
     * [throwInvalidAdapterException Throw an exception if the adapter or driver is invalid, does not exist or is not supported by PDO.
     *
     * @param [string] $adaptar [The adaptar that should be used for the PDO connection.]
     *
     * @return [type] [An inherited PDO exception with a customized message.]
     */
    public function throwInvalidAdapterException($adaptar)
    {
        $message = "Invalid Adapter $adaptar : Please provide a driver for the connection to the database.";
        throw new InvalidAdaptarException($message);
    }
}
