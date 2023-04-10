<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use Symfony\Component\HttpFoundation\Response;


class SubscriberControllerTest extends AbstractControllerTest
{

    // В данном случае все что нам нужно это отправить запрос и получить в обратку 200
    public function testSubscribe(): void
    {
        // Подготавливаем наш контент для получения кода 200
        $content = json_encode(["email" => "test1@test.com", "agreed" => true]);

        // Отправляем пост запрос
        $this->client->request('POST','/api/v1/subscribe',[],[],[],$content);

        $this->assertResponseIsSuccessful();

    }

    public function testSubscribeNotAgreed(): void
    {
        $content = json_encode(["email" => "test@test.com"]);

        $this->client->request('POST','/api/v1/subscribe',[],[],[],$content);

        $responseContent = json_decode($this->client->getResponse()->getContent());

        // Проверяем что код ответа соответствует ожидаемому (400)
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        // Нам нужно удостовериться что мы получили то что у нас действительно есть violations и что поле
        // в котором произошла ошибка действительно поле agreed.

        $this->assertJsonDocumentMatches($responseContent,[
            // Сравниваем message
            "$.message" => "validation failed",
            // Нам главное понять что у нас впринципе есть ошибки и что она у нас одна
            "$.details.violations" => self::countOf(1),
            // И посмотреть что поле в этой одной ошибке это agreed
            "$.details.violations[0].field" => "agreed",
        ]);

    }








}