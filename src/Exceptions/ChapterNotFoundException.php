<?php

namespace App\Exceptions;

use RuntimeException;

class ChapterNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("Chapter not found");
    }
}