<?php

namespace App\Exceptions;

use RuntimeException;

class BookCategoryNotEmptyException extends RuntimeException
{

    public function __construct(int $amountBooks)
    {
        parent::__construct("you cant delete category because it has" . $amountBooks . "in it" );
    }
}