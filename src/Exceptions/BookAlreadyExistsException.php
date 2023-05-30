<?php

namespace App\Exceptions;

use RuntimeException;

class BookAlreadyExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('book already exists');
    }


}