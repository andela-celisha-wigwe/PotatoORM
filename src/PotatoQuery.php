<?php

namespace Elchroy\PotatoORM;

// require '../vendor/autoload.php';

use PDO;

class PotatoQuery
{
    public static $connection;

    public function __construct($con = null)
    {
        if ($con == null) {
            self::$connection = (new PotatoConnector())::$connection;
        } else {
            self::$connection = $con;
        }

    }

    public function getFrom($table, $columns = '*')
    {
        $sql = "SELECT $columns FROM $table";
        $statement = self::$connection->prepare($sql);
        $statement->execute();
        $result = $statement->fetchAll(PDO::FETCH_OBJ);

        return $result;
    }

    public function getOne($table, Int $id)
    {
        $sql = "SELECT * FROM $table WHERE id = :id ";
        $statement = self::$connection->prepare($sql);
        $statement->bindParam(':id', $id);
        $statement->execute();

        return $result = $statement->fetchObject($table); // convert the object argument to
    }

    public function storeIn($table, array $data)
    {
        $columnsString = $this->getColumns($data);
        $count = (int) count($data);
        $sql = "INSERT INTO $table $columnsString VALUES (".$this->putQuesMarks($count).')';
        $statement = self::$connection->prepare($sql);
        $this->setBindForInsert($statement, array_values($data));
        if ($statement->execute() == false) {
            return false;
        }
        return $statement->execute();
        // $lastInserted = $statement->lastInsertId();
        // return $this->getOne($table, $lastInserted);
        // AFter inserting, the item that was inserted should be returned.
    }

    public function deleteFrom($table, Int $id)
    {
        $sql = "DELETE FROM $table WHERE id = :id ";
        $statement = self::$connection->prepare($sql);
        $statement->bindParam(':id', $id);
        if ($statement->execute() == false) {
            return false;
        }

        return $statement->execute();
    }

    public function updateAt($table, array $data)
    {
        $id = (int) $data['id']; // store the id in a variable.
        unset($data['id']); // remove the id key from the array. ID is to be used for the update location.
        $upd = (string) $this->makeModify(array_keys($data)); // genertate the columns for the update statement.
        $sql = "UPDATE {$table} SET ".$upd.' WHERE id = :id_val';
        $statement = self::$connection->prepare($sql);
        $this->setBindForUpdate($statement, $data);
        $statement->bindValue(':id_val', $id);
        if ($statement->execute() == false) {
            return false;
        }

        return $statement->execute();
    }

    public function setBindForUpdate($statement, array $data)
    {
        $count = count($data);
        foreach ($data as $key => $value) {
            $statement->bindValue(":$key".'_val', $value);
        }
    }

    public function makeModify(array $columns)
    {
        // make sure that you remove the id from this upd
        $count = count($columns);
        $upd = '';
        foreach ($columns as $key) {
            $upd .= $key.' = '.':'.$key.'_val, ';
        }

        return $upd = trim($upd, ', ');
    }

    public function putQuesMarks($count)
    {
        $str = '';
        for ($i = 0; $i < $count; $i++) {
            $str .= '?, ';
        }

        return $str = trim($str, ', '); // remove the last comma.
    }

    public function setBindForInsert($statement, $values)
    {
        $count = count($values);
        for ($i = 1; $i <= $count; $i++) {
            $statement->bindParam(($i), $values[$i - 1]);
        }
    }

    public function getColumns(array $data)
    {
        return '('.implode(', ', array_keys($data)).')';
    }
}
