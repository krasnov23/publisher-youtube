<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends WebTestCase
{

    public function testBooksByCategories()
    {

        // Создали клиента
        $client = static::createClient();

        // Создаем запрос который будем отсылать
        $client->request('GET','api/v1/category/11/books');

        // getResponse - Объект ответа, getContent - содержимое, т.е фактически json который нам вернул контролер
        $responseContent = $client->getResponse()->getContent();

        // Проверяем что код ответа 200
        $this->assertResponseIsSuccessful();

        //
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/responses/BookControllerTest_testBooksByCategories.json'
            ,$responseContent);


    }


}
