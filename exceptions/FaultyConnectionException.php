<?php

namespace Elchroy\PotatoORMExceptions;

class FaultyConnectionException extends \PDOException
{
    public function __construct($message)
    {
        echo $message."\n";
    }
}