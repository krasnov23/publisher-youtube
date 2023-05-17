<?php

namespace App\Exceptions;

use App\Models\IdResponse;
use App\Models\SignUpRequest;
use JetBrains\PhpStorm\Internal\LanguageLevelTypeAware;
use Symfony\Component\Asset\Exception\RuntimeException;

class UserAlreadyExistsException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('user already exists');
    }

}