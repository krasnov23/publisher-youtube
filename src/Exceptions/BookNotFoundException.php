<?php

namespace App\Exceptions;

use RuntimeException;

class BookNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book not found' );
    }
}