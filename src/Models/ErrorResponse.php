<?php

namespace App\Models;

use Nelmio\ApiDocBundle\Annotation\Model;
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


    /**
     * @OA\Property(type="object", oneOf={
     * @OA\Schema(ref=@Model(type=ErrorDebugDetails::class)),
     * @OA\Schema(ref=@Model(type=ErrorValidationDetails::class)),
     * })
     */
    public function getDetails(): mixed
    {
        return $this->details;
    }


}