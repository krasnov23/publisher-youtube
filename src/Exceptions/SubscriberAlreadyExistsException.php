<?php

namespace App\Exceptions;

class SubscriberAlreadyExistsException extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Subscriber not found by email', );
    }

}