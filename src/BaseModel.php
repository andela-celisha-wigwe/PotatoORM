<?php


class BaseModel
{
    protected static $connection;
    // $sql = "INSERT INTO {$table}s ($propertiesString) VALUES ('$this->name', $this->price);";
    // $sql = "SELECT * FROM {$class}s WHERE id = :id";
    // $sql = "UPDATE {$table}s SET name=?, price=? WHERE id=?";
    // $sql = "DELETE FROM {$table}s WHERE id=:id";

    public function __construct()
    {
        var_dump(self::$connection);
        if (!isset(self::$connection)) {
            self::$connection = $this::connect();
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public static function connect()
    {
        $config = self::getConfigurations();
        $host = $config['host'];
        $dbname = $config['dbname'];
        $username = $config['username'];
        $password = $config['password'];
        $adaptar = $config['adaptar'];
        self::$connection = new PDO("$adaptar:host=$host;dbname=$dbname", $username, $password);
    }

    public static function getAll()
    {
        self::connect(); // set the sonnection
            $table = self::getClassTableName();
        $sql = "SELECT * FROM $table ;";
            // var_dump($sql);
            $stmt = self::$connection->prepare($sql);
        $stmt->execute();

        return $results = $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    protected static function getClassTableName()
    {
        return strtolower(get_called_class());
    }

    protected static function getConfigurations()
    {
        return parse_ini_file('../config.ini');
    }
}

class Dog extends BaseModel
{
}

class Book extends BaseModel
{
}
var_dump(Dog::getAll());

// new Dog();

// BaseModel::connect();
