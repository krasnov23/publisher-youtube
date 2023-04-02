<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;

class SubscriberControllerTest extends AbstractControllerTest
{

    // В данном случае все что нам нужно это отправить запрос и получить в обратку 200
    public function testSubscribe(): void
    {
        // Подгатавливаем наш контент для получения кода 200
        $content = json_encode(['email' => 'test2@test.com','agreed' => true]);

        // Отправляем пост запрос
        $this->client->request('POST','/api/v1/subscribe',[],[],[],$content);

        $this->assertResponseIsSuccessful();

    }



}