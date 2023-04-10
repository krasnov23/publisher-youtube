<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class BookCategoryNotFoundException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Book category not found' );
    }
}
