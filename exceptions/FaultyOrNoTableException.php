<?php

namespace Elchroy\PotatoORMExceptions;

class FaultyOrNoTableException extends \PDOException
{
    public function __construct($message)
    {
        echo $message."\n";
        // $this->getMessage();
    }
}