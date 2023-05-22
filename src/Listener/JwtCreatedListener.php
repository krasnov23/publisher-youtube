<?php

namespace App\Listener;

use App\Entity\UserApi;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        /** @var UserApi $user */
        $user = $event->getUser();

        $payload = $event->getData();

        $payload['id'] = $user->getId();

        // Метод в котором мы изменяем добавляем к email авторизацию по id
        $event->setData($payload);

    }
}