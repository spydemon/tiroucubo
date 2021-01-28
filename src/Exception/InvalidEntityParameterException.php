<?php

namespace App\Exception;

use Exception;

class InvalidEntityParameterException extends Exception
{
    private object $entity;

    public function __construct(string $message, object $entity, int $code = 0, Throwable $previous = null)
    {
        $this->entity = $entity;
        return parent::__construct($message, $code, $previous);
    }

    public function getEntity() : object
    {
        return $this->entity;
    }
}
