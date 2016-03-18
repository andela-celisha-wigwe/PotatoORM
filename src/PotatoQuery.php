<?php

namespace Elchroy\PotatoORM;

use Elchroy\PotatoORMExceptions\FaultyExecutionException;
use Elchroy\PotatoORMExceptions\FaultyOrNoTableException;
use PDO;
use PDOException;

class PotatoQuery
{
    public $connection;

    /**
     * Setup the connection to aid all queries.
     *
     * @param PotatoConnector|null $connector [description]
     */
    public function __construct(PotatoConnector $connector = null)
    {
        $connector = $connector == null ? new PotatoConnector() : $connector;
        $this->connection = $connector->setConnection();
    }

    /**
     * Get all records from the specified database table.
     *
     * @param string $table   The table name
     * @param string $columns The columns to be gotten from the database table
     *
     * @return array An array of objects, each representing a record fetched from the database table.
     */
    public function getFrom($table, $columns = '*')
    {
        $sql = "SELECT $columns FROM $table";
        $statement = $this->connection->prepare($sql);
        if ($statement == false) {
            $this->throwFaultyOrNoTableException($table); // If the SQL query cannot be prepared correctly, then an errir ius thrown
        }
        $execution = $this->tryExecuting($statement);
        $result = $statement->fetchAll(PDO::FETCH_CLASS);
        if (count($result) < 1) {
            return "No records found in this table ($table).";
        }

        return $result;
    }

    /**
     * Get only one record from the datbase table, given the id of the record to be retrieved.
     *
     * @param string $table The database table name
     * @param int    $id    The id of the recored to be fethced.
     *
     * @return model object        The record fetched form the database table as an object
     */
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
        if ($result == false) {
            echo "Record $id : Not found found in this table ($table).\n";
        }

        return $result;
    }

    /**
     * Store a new recored inside athe database table.
     *
     * @param string $table The table into which it is required to add a new record
     * @param string $data  An aray of the columns to be used for inseting into the database
     *
     * @return bool The result of the query execution. True if successful. False otherwise.
     */
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

        return $execution;
        // return $this->getOne($table, self::$connection->lastInsertId());
    }

    /**
     * Try executoing a query given the statment.
     *
     * @param PDOStatemetn $statement The PDO statment to be executed.
     *
     * @return bool The result of executing a query. True for success, false for failure
     */
    public function tryExecuting($statement)
    {
        try {
            $execution = $statement->execute();
        } catch (PDOException $e) {
            $message = $e->getMessage();
            $this->throwFaultyExecutionException($message);
        }

        return $execution;
    }

    /**
     * Throw an exception when there is a problem with execution of a statement.
     *
     * @param string $message The message to be related in the event of this exception
     *
     * @return [type] [description]
     */
    public function throwFaultyExecutionException($message)
    {
        throw new FaultyExecutionException($message);
    }

    /**
     * The columns to be inserted into the SQL query.
     *
     * @param array $data An array of the columns to be used for inserting int ot he database.
     *
     * @return string A string made up of the columns for insert query enclode in parentheses
     */
    public function getColumns(array $data)
    {
        return '('.implode(', ', array_keys($data)).')';
    }

    /**
     * Bind parameters to the placeholders in the SQL queries. This is to avoid sql injection.
     *
     * @param [type]  $statement The PDOStatement to be used to the binding.
     * @param [array] $values    The result of binding parameters to an SQL query.
     */
    public function setBindForInsert($statement, $values)
    {
        $count = count($values);
        for ($i = 1; $i <= $count; $i++) {
            $statement->bindParam(($i), $values[$i - 1]);
        }
    }

    /**
     * Delete a record form a database table.
     *
     * @param [string] $table The table to be deleted from
     * @param [int]    $id    the ID of the record to be deleted
     *
     * @return [type] The result of the deletion execution.
     */
    public function deleteFrom($table, $id)
    {
        $sql = "DELETE FROM $table WHERE id = :id ";
        $statement = $this->connection->prepare($sql);
        if ($statement == false) {
            $this->throwFaultyOrNoTableException($table);
        }
        $statement->bindParam(':id', $id);
        $execution = $this->tryExecuting($statement);

        return $execution;
    }

    /**
     * Update records in the datbase table.
     *
     * @param string  $table The table who record ois to be updated
     * @param [array] $data  [The update data in an array]
     *
     * @return [bool] [The result of executing an SQL update query. TRUE OR FALSE]
     */
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

        return $execution;
    }

    /**
     * Bind parameters to the placeholders in the SQL queries. This is to avoid sql injection.
     *
     * @param [type]  $statement The PDOStatement to be used to the binding.
     * @param [array] $values    The result of binding parameters to an SQL query.
     */
    public function setBindForUpdate($statement, array $data)
    {
        $count = count($data);
        foreach ($data as $key => $value) {
            $statement->bindValue(":$key".'_val', $value);
        }
    }

    /**
     * [makeModify Modify the query string to enable the binding for update.
     *
     * @param array $columns [The columns to be updated]
     *
     * @return [string] [The string of the columns to be updated and their corresponidng value_tags/ placeholders] E.g name = :name-val
     */
    public function makeModify(array $columns)
    {
        // make sure that you remove the id from this upd
        $count = count($columns);
        $updateString = ''; // Start with an empty string
        foreach ($columns as $column) {
            $updateString .= $column.' = '.':'.$column.'_val, ';
        }

        return $updateString = trim($updateString, ', ');
    }

    /**
     * [putQuesMarks Prepare a set of question-marks to comple an insert query SQL string. THis enables queryh binding.
     *
     * @param int $count The numbe rof column to be inserted
     *
     * @return string A string with the number of  question marks needed for a complete insert query. E.g "?, ?"
     */
    public function putQuesMarks($count)
    {
        $str = '';
        for ($i = 0; $i < $count; $i++) {
            $str .= '?, ';
        }

        return $str = trim($str, ', '); // remove the last comma.
    }

    /**
     * [throwFaultyOrNoTableException Throw an exception if the table does not exist.
     *
     * @param string $table The table that foes not exist.
     *
     * @return [type] An exception with a custom message displayed when the databse table does not exist.
     */
    public function throwFaultyOrNoTableException($table)
    {
        $message = "There seems to be a problem. Please confirm if the '$table' table exists in the database."; // Create a custom exception message.
        throw new FaultyOrNoTableException($message);
    }
}
