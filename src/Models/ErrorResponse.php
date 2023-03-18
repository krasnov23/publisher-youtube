<?php

namespace App\Models;

class ErrorResponse
{
    // details = null, на тот случай если мы захотим из сервиса или из контроллера возвращать ерор респонс, чтобы нам не
    // нужно было всегда указывать детали.
    public function __construct(private string $message, private mixed $details = null)
    {
    }

    public function getMessage(): string
    {
        return $this->message;
    }


    public function getDetails(): mixed
    {
        return $this->details;
    }


}