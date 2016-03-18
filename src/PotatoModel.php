<?php

namespace Elchroy\PotatoORM;

class PotatoModel
{
    /**
     * [$queryTo The query (instance of PotatoQuery) to handle all database queries.
     *
     * @var [type] Instance of potatoQuery class, to be difined during construction.
     */
    public $queryTo;

    /**
     * [$dataToSave The properties of a object of theis class to be saved in the database.
     *
     * @var array This is an array of property names to be saved in to the database.
     */
    public $dataToSave = [];

    /**
     * [__construct The constructor to initialize public variables].
     *
     * @param PotatoQuery|null $potatoQuery The query to be used for the communication with the databse.
     */
    public function __construct(PotatoQuery $potatoQuery = null)
    {
        $this->queryTo = $potatoQuery == null ? new PotatoQuery() : $potatoQuery;
    }

    /**
     * [__set magic Property to set put of the object into the arary of properties to be saved in the database.].
     *
     * @param [type] $property [The property of the object to be saved.]
     * @param [type] $value    [The value of given property to be saved.]
     */
    public function __set($property, $value)
    {
        $this->dataToSave[$property] = $value;
    }

    /**
     * [__get Magic function to get the property from the dataToSave array, the array of properties.].
     *
     * @param [type] $property [The property to be gotten from the data to save array of propoerties.]
     *
     * @return [type] [If the property is cound, return the value of that property. Otherwise throw an exception]
     */
    public function __get($property)
    {
        if (array_key_exists($property, $this->dataToSave)) {
            return $this->dataToSave[$property];
        }

        return "$property not found.";
        // Throw an exception
        // echo "Inside the get method.";
    }

    /**
     * [__isset Magic function to check if the given propertyh of the object is set.].
     *
     * @param [type] $property [The property in question]
     *
     * @return bool True if the property exists. False otherwise.
     */
    public function __isset($property)
    {
        return isset($this->dataToSave[$property]);
    }

    /**
     * [getAll Get all records of database table corresponding the the called class name.
     *
     * @param [type] $query [The query to be used to communicated with the databse.]
     *
     * @return [type] Return the returned value of the query's query. The return value is an array of records represented as objects.
     */
    public static function getAll($query = null)
    {
        if ($query == null) {
            $query = new PotatoQuery();
        }
        $table = self::getClassTableName();

        return $query->getFrom($table);
    }

    /**
     * [find Find on record or row in the table given by an ID.
     *
     * @param [type] $id    [the ID of the row record to be fetched.]
     * @param [type] $query [The query to be used for the database communication.]
     *
     * @return [type] [The returned value of finding the record with the given ID.]
     */
    public static function find($id, $query = null)
    {
        if ($query == null) {
            $query = new PotatoQuery();
        }
        $table = self::getClassTableName();

        return $query->getOne($table, $id);
    }

    /**
     * SAve a recrd into the database. Saveing is either inserting or updating.
     *
     * @return [type] [A newly inserted record ot a recently updated record in the database.]
     */
    public function save()
    {
        return $this->isStored() ? $this->update() : $this->insert();
    }

    /**
     * Check if the object to be saved has an ID.
     *
     * @return bool [description]
     */
    public function isStored()
    {
        return isset($this->id);
    }

    /**
     * Insert a new record into the database while communicating with the qury handler.
     *
     * @return [type] [The returned value of the quering the databse.]
     */
    public function insert()
    {
        $table = self::getClassTableName();

        return $this->queryTo->storeIn($table, $this->dataToSave);

        // return $this; // return the inserted object.
    }

    /**
     * Update a record in a database. The record must have an ID before it can be updated.
     *
     * @return [type] [description]
     */
    public function update()
    {
        $table = self::getClassTableName();

        return $this->queryTo->updateAt($table, $this->dataToSave);

        // return $this; // Return the updated ogject.
    }

    /**
     * Destroy a recored or row from a database table.
     *
     * @param [type] $id    [The ID of the record to be deleted.]
     * @param [type] $query [The query to handle to deleting operation]
     *
     * @return [type] [An array of all the records in that table without the deleted recode.]
     */
    public static function destroy($id, $query = null)
    {
        if ($query == null) {
            $query = new PotatoQuery();
        }
        $table = self::getClassTableName();
        $query->deleteFrom($table, $id);

        return self::getAll(); // return all the items after the previous item has been deleted.
    }

    /**
     * [getClassTableName Get the name of the class from which this function is called, including classes that inherit from this class.].
     *
     * @return [type] [description]
     */
    public static function getClassTableName()
    {
        $tableWithNameSpance = strtolower(get_called_class());
        $table = explode('\\', $tableWithNameSpance);

        return end($table);
    }
}
