<?php

namespace App\Exceptions;

use RuntimeException;

class UploadFileInvalidTypeException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('upload type file is invalid');
    }


}