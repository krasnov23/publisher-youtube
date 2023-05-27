<?php

namespace App\Exceptions;

use RuntimeException;

class UserNotFoundException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("User not found");
    }


}