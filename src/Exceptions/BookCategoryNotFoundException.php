<?php

namespace App\Exceptions;


use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class BookCategoryNotFoundException extends \RunTimeException
{

    public function __construct()
    {
        parent::__construct('Book category doesnt found', Response::HTTP_NOT_FOUND, null);
    }

}
