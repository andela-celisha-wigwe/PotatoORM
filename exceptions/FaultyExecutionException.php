<?php

namespace Elchroy\PotatoORMExceptions;

class FaultyExecutionException extends \PDOException
{
    public function __construct($message)
    {
        echo "Execution Problem - Check that database column names match with the names of the properties to be saved.\n";
        echo "Confirm if listed columns accept individual data types.\n";
        echo $message."\n";
    }
}