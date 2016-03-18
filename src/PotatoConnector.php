<?php

namespace Elchroy\PotatoORM;

use Elchroy\PotatoORMExceptions\FaultyConnectionException;
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
        $this->configuration = $configData == null ? $this->getConfigurations() : $configData;
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
            $connection = new PDO("$adaptar:host=$host;dbname=$dbname", $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $this->throwFaultyConnectionException($message);
        }

        return $connection;
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
        if ($filepath == null) {
            $filepath = $this->getConfigFilePath();
        }

        return parse_ini_file($filepath);
    }

    /**
     * [getConfigFilePath Get the file path of the file where the configuration lies.].
     *
     * @return [string] [The file path of the location where the confuiguration information lie.]
     */
    public function getConfigFilePath()
    {
        return __DIR__.'/../config.ini';
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
     * @return [type] [An inherited PDO exception]
     */
    public function throwFaultyConnectionException($message)
    {
        throw new FaultyConnectionException($message);
    }
}
