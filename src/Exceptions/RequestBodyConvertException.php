<?php

namespace App\Exceptions;


class RequestBodyConvertException extends \RunTimeException
{

    public function __construct(\Throwable $previous)
    {
        parent::__construct('error with serialization',0, $previous);
    }

}