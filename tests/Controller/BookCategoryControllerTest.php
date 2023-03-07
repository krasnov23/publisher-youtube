<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookCategoryControllerTest extends WebTestCase
{
    public function testIndex()
    {
        // Создали клиента
        $client = static::createClient();

        // Создаем запрос который будем отсылать
        $client->request('GET', 'api/v1/book/categories');

        // getResponse - Объект ответа, getContent - содержимое, т.е фактически json который нам вернул контролер
        $responseContent = $client->getResponse()->getContent();

        // Проверяем что код ответа 200
        $this->assertResponseIsSuccessful();

        $this->assertJsonStringEqualsJsonFile(
            __DIR__.'/responses/BookCategoryControllerTest_testCategories.json', $responseContent);
    }
}
