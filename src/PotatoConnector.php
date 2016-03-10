<?php

namespace Elchroy\PotatoORM;

use PDO;

class PotatoConnector
{
    public static $connection;
    public static $conn;

    public function __construct()
    {
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

    private static function connect($adaptar, $host, $dbname, $username, $password)
    {
        $connection = new PDO("$adaptar:host=$host;dbname=$dbname", $username, $password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }

    public static function getConfigurations()
    {
        return $config = parse_ini_file(__DIR__.'/../config.ini');
    }

    private static function getAdaptar()
    {
        return self::getConfigurations()['adaptar'];
    }

    private static function getHost()
    {
        return self::getConfigurations()['host'];
    }

    private static function getDBName()
    {
        return self::getConfigurations()['dbname'];
    }

    private static function getUsername()
    {
        return self::getConfigurations()['username'];
    }

    private static function getPassword()
    {
        return self::getConfigurations()['password'];
    }
}
