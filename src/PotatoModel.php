<?php

namespace Elchroy\PotatoORM;

class PotatoModel
{
    public static $buildTo;
    public static $tableName;

    public static function getAll()
    {
        $queryTo = new PotatoQuery();
        $table = self::getClassTableName();
        return $queryTo->getFrom($table);
    }

    public static function find(Int $id)
    {
        $queryTo = new PotatoQuery();
        $table = self::getClassTableName();
        return $queryTo->getOne($table, $id);
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
        $queryTo = new PotatoQuery();
        $table = self::getClassTableName();
        $queryTo->storeIn($table, (array) $this);
        return $this; // return the inserted object.
    }

    public function update()
    {
        $queryTo = new PotatoQuery();
        $table = self::getClassTableName();
        $queryTo->updateIn($table, (array) $this);
        return $this; // Return the updated ogject.
    }

    public static function destroy(Int $id)
    {
        $queryTo = new PotatoQuery();
        $table = self::getClassTableName();
        $queryTo->deleteFrom($table, $id);
        return self::getAll(); // return all the items after the previous item has been deleted.
    }

    protected static function getClassTableName()
    {
        $tableWithNameSpance = strtolower(get_called_class());
        $table = explode("\\", $tableWithNameSpance);
        return (end($table));
    }
}
