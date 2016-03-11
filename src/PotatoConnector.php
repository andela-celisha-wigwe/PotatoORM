<?php

namespace Elchroy\PotatoORM;

use PDO;

class PotatoConnector
{
    public static $connection;
    public $configuration;

    public function __construct($configurationData = null)
    {
        if ($configurationData == null) {
            $configurationData = self::getConfigurations();
        }
        $this->configuration = $configurationData;
    }

    public static function setConnection()
    {
        $adaptar = self::getAdaptar();
        $host = self::getHost();
        $dbname = self::getDBName();
        $username = self::getUsername();
        $password = self::getPassword();
        self::$connection = self::connect($adaptar, $host, $dbname, $username, $password);

        return self::$connection;
    }

    public static function connect($adaptar = null, $host = null, $dbname = null, $username = null, $password = null)
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
        $connection = new PDO("$adaptar:host=$host;dbname=$dbname", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }

    public static function getConfigurations($filepath = null)
    {
        if ($filepath == null) {
            $filepath = __DIR__.'/../config.ini';
        }
        return parse_ini_file($filepath);
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
}
