<?php

namespace Elchroy\PotatoORMExceptions;

class FaultyOrNoTableException extends \PDOException
{
    public $message;

    public function __construct($message)
    {
        $this->message = $message;

        return $this->message;
    }
}
