<?php

namespace Elchroy\PotatoORM;

use Elchroy\PotatoORMExceptions\FaultyOrNoTableException;
use Elchroy\PotatoORMExceptions\FaultyExecutionException;

// require '../vendor/autoload.php';

use PDO;
use PDOException;

class PotatoQuery
{
    public $connection;

    public function __construct(PotatoConnector $connector = null)
    {
        if ($connector == null) {
            $connector = new PotatoConnector();
        }
        $this->connection = $connector->setConnection();
    }

    public function getFrom($table, $columns = '*')
    {
        $sql = "SELECT $columns FROM $table";
        $statement = $this->connection->prepare($sql);
        if ($statement == false) {
            $this->throwFaultyOrNoTableException($table);
        }
        $execution = $this->tryExecuting($statement);
        $result = $statement->fetchAll(PDO::FETCH_CLASS);
        if (count($result) < 1) {
            return "No records found in this table ($table).";
        }

        return $result;
    }

    public function getOne($table, $id)
    {
        $sql = "SELECT * FROM $table WHERE id = :id ";
        $statement = $this->connection->prepare($sql);
        if ($statement == false) {
            $this->throwFaultyOrNoTableException($table);
        }
        $statement->bindParam(':id', $id);
        $execution = $this->tryExecuting($statement);
        $result = $statement->fetchObject($table);
        // $result = $statement->fetch(PDO::FETCH_OBJ);
        if ($result == false) {
            echo "Throw Fetching Exception. Record $id : Not found found in this table ($table).";
        }
        return $result;
    }

    public function storeIn($table, $data)
    {
        $columnsString = $this->getColumns($data);
        $count = (int) count($data);
        $sql = "INSERT INTO $table $columnsString VALUES (".$this->putQuesMarks($count).')';
        $statement = $this->connection->prepare($sql);
        if ($statement == false) {
            $this->throwFaultyOrNoTableException($table);
        }
        $this->setBindForInsert($statement, array_values($data));
        $execution = $this->tryExecuting($statement);
        echo "Saved Successfully.\n";
        return $execution;
        // return $this->getOne($table, self::$connection->lastInsertId());
    }

    public function tryExecuting($statement)
    {
        try {
            $execution = $statement->execute();
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $this->throwFaultyExecutionException($message);
        }
        // var_dump($execution);

        return $execution;
    }

    public function throwFaultyExecutionException($message)
    {
        throw new FaultyExecutionException($message);
    }

    public function getColumns(array $data)
    {
        return '('.implode(', ', array_keys($data)).')';
    }

    public function setBindForInsert($statement, $values)
    {
        $count = count($values);
        for ($i = 1; $i <= $count; $i++) {
            $statement->bindParam(($i), $values[$i - 1]);
        }
    }

    public function deleteFrom($table, $id)
    {
        $sql = "DELETE FROM $table WHERE id = :id ";
        $statement = $this->connection->prepare($sql);
        if ($statement == false) {
            $this->throwFaultyOrNoTableException($table);
        }
        $statement->bindParam(':id', $id);
        $execution = $this->tryExecuting($statement);
        echo "Deleted Successfully.\n";

        return $execution;

    }

    public function updateAt($table, $data)
    {
        $id = (int) $data['id']; // store the id in a variable.
        unset($data['id']);
        $upd = (string) $this->makeModify(array_keys($data)); // genertate the columns for the update statement.
        $sql = "UPDATE {$table} SET ".$upd.' WHERE id = :id_val';
        $statement = $this->connection->prepare($sql);
        if ($statement == false) {
            $this->throwFaultyOrNoTableException($table);
        }
        $this->setBindForUpdate($statement, $data);
        $statement->bindValue(':id_val', $id);
        $execution = $this->tryExecuting($statement);
        echo "Updated Successfully.\n";

        return $execution;
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

    public function throwFaultyOrNoTableException($table)
    {
        $message = "There seems to be a problem. Please confirm if the '$table' table exists in the database.";
        throw new FaultyOrNoTableException($message);
    }
}
