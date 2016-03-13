<?php

namespace Elchroy\PotatoORM;

use PDO;
Use PDOException;
use Elchroy\PotatoORMExceptions\FaultyConnectionException;

class PotatoConnector
{
    public $connection;
    public $configuration;

    public function __construct($configurationData = null, $connection = null)
    {
        if ($configurationData == null) {
            $configurationData = $this->getConfigurations();
        }
        if ($connection = null) {
            $connection = $this->connect();
        }
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

    public function connect($adaptar = null, $host = null, $dbname = null, $username = null, $password = null)
    {
        if (is_null($adaptar)) {
            $adaptar = $this->getAdaptar();
        }
        if (is_null($host)) {
            $host = $this->getHost();
        }
        if (is_null($dbname)) {
            $dbname = $this->getDBName();
        }
        if (is_null($username)) {
            $username = $this->getUsername();
        }
        if (is_null($adaptar)) {
            $password = $this->getPassword();
        }
        try {
            $connection = new PDO("$adaptar:host=$host;dbname=$dbname", $username, $password);
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $this->throwFaultyConnectionException($message);
            exit;
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
