<?php

namespace Elchroy\PotatoORM;

use Elchroy\PotatoORMExceptions\FaultyConnectionException;
use PDO;
use PDOException;

class PotatoConnector
{
    public $connection;
    public $configuration;

    public function __construct(array $configurationData = null, PDO $connection = null)
    {
        // if ($configurationData == null) {
        //     $configurationData = $this->getConfigurations();
        // }
        // if ($connection = null) {
        //     $connection = $this->connect();
        // }
        $configurationData = ($configurationData == null ? $this->getConfigurations() : $configurationData);
        $this->connection = $connection;
        $this->configuration = $configurationData;
    }

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

    public function getConfigurations($filepath = null)
    {
        if ($filepath == null) {
            $filepath = $this->getConfigFilePath();
        }

        return parse_ini_file($filepath);
    }

    public function getConfigFilePath()
    {
        return __DIR__.'/../config.ini';
    }

    public function getAdaptar()
    {
        return $this->configuration['adaptar'];
    }

    public function getHost()
    {
        return $this->configuration['host'];
    }

    public function getDBName()
    {
        return $this->configuration['dbname'];
    }

    public function getUsername()
    {
        return $this->configuration['username'];
    }

    public function getPassword()
    {
        return $this->configuration['password'];
    }

    public function throwFaultyConnectionException($message)
    {
        throw new FaultyConnectionException($message);
    }
}
