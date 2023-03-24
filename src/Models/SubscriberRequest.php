<?php

namespace App\Models;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class SubscriberRequest
{

    // Валидируте данное свойство как Email
    #[Email]
    #[NotBlank]
    private string $email;


    // агрид всегда будет тру
    #[IsTrue]
    #[NotBlank]
    private bool $agreed;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function isAgreed(): bool
    {
        return $this->agreed;
    }

    public function setAgreed(bool $agreed): void
    {
        $this->agreed = $agreed;
    }

}