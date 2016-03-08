<?php

namespace Elchroy\PotatoORM;

use \PDO;

class PotatoConnector
{
    public static $connection;
    public static $conn;

    public function __construct()
    {
        $adaptar = self::getAdaptar();
        $host = self::getHost();
        $dbname = self::getDBName();
        $username = self::getUsername();
        $password = self::getPassword();
        self::$connection = self::connect($adaptar, $host, $dbname, $username, $password);
    }
    private function connect($adaptar, $host, $dbname, $username, $password)
    {
        $connection = new PDO("$adaptar:host=$host;dbname=$dbname", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    public function getConfigurations()
    {
        return $config = parse_ini_file(__DIR__.'/../config.ini');
    }

    private function getAdaptar()
    {
        return $this->getConfigurations()['adaptar'];
    }

    private function getHost()
    {
        return $this->getConfigurations()['host'];
    }

    private function getDBName()
    {
        return $this->getConfigurations()['dbname'];
    }

    private function getUsername()
    {
        return $this->getConfigurations()['username'];
    }

    private function getPassword()
    {
        return $this->getConfigurations()['password'];
    }
}
