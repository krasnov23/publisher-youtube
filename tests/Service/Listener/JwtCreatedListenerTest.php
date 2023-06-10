<?php

namespace App\Tests\Service\Listener;

use App\Listener\JwtCreatedListener;
use App\Tests\AbstractTestCase;
use App\Tests\Service\MockUtils;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JwtCreatedListenerTest extends AbstractTestCase
{
    public function testInvoke(): void
    {
        $user = MockUtils::createUser();
        $this->setEntityId($user,1);

        $listener = new JwtCreatedListener();
        $event = new JWTCreatedEvent([],$user,[]);

        $listener($event);

        $this->assertEquals(['id' => 1],$event->getData());

    }


}