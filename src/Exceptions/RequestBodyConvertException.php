<?php

namespace App\Exceptions;


class RequestBodyConvertException extends \RunTimeException
{

    public function __construct(\Throwable $previous)
    {
        parent::__construct('subscriber already exists',0, $previous);
    }

}