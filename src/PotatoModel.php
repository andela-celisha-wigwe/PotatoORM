<?php

namespace Elchroy\PotatoORM;

class PotatoModel
{
    public $queryTo;
    public $dataToSave = [];

    public function __construct(PotatoQuery $potatoQuery = null)
    {
        // $this->queryTo = $potatoQuery;
        $this->queryTo = $potatoQuery == null ? new PotatoQuery() : $potatoQuery;
    }

    public function __set($property, $value)
    {
        $this->dataToSave[$property] = $value;
    }

    public function __get($property)
    {
        if (array_key_exists($property, $this->dataToSave)) {
            return $this->dataToSave[$property];
        }
        return "$property not found.";
        // Throw an exception
        // echo "Inside the get method.";
    }

    public function __isset($property)
    {
        return isset($this->dataToSave[$property]);
    }

    public static function getAll($query = null)
    {
        if ($query == null) {
            $query = new PotatoQuery();
        }
        $table = self::getClassTableName();

        return $query->getFrom($table);
    }

    public static function find($id, $query = null)
    {
        if ($query == null) {
            $query = new PotatoQuery();
        }
        $table = self::getClassTableName();

        return $query->getOne($table, $id);
    }

    public function save()
    {
        return $this->isStored() ? $this->update() : $this->insert();
    }

    public function isStored()
    {
        return isset($this->id);
    }

    public function insert()
    {
        $table = self::getClassTableName();

        return $this->queryTo->storeIn($table, $this->dataToSave);

        // return $this; // return the inserted object.
    }

    public function update()
    {
        $table = self::getClassTableName();

        return $this->queryTo->updateAt($table, $this->dataToSave);

        // return $this; // Return the updated ogject.
    }

    public static function destroy($id, $query = null)
    {
        if ($query == null) {
            $query = new PotatoQuery();
        }
        $table = self::getClassTableName();
        $query->deleteFrom($table, $id);

        return self::getAll(); // return all the items after the previous item has been deleted.
    }

    public static function getClassTableName()
    {
        $tableWithNameSpance = strtolower(get_called_class());
        $table = explode('\\', $tableWithNameSpance);

        return end($table);
    }
}
