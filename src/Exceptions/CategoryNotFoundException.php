<?php

namespace App\src\Exceptions;

use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use RuntimeException;

class CategoryNotFoundException extends RuntimeException
{

    public function __construct()
    {
        parent::__construct("Category not found");
    }

}