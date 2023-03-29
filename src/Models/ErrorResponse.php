<?php

namespace App\Models;

use OpenApi\Annotations as OA;


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

    // Уточняем что в OA передает типа объект
    /**
     * @OA\Property(type="object")
     */
    public function getDetails(): mixed
    {
        return $this->details;
    }


}