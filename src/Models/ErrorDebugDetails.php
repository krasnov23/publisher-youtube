<?php

namespace App\Models;

class ErrorDebugDetails
{
    // Данный класс на вход будет принимать только trace
    public function __construct(private string $trace)
    {

    }

    public function getTrace(): string
    {
        return $this->trace;
    }

}