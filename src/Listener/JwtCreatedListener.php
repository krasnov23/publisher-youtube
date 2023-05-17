<?php

namespace App\Listener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();
        $payload = $event->getData();
        $payload['id'] = $user->getUserIdentifier();

        // Метод в котором мы изменяем авторизацию по email на авторизацию по id
        $event->setData($payload);

    }
}