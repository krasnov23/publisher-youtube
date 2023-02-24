<?php

namespace App\tests\Controller;

use App\Controller\BookCategoryController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookCategoryControllerTest extends WebTestCase
{

    public function testIndex()
    {

        // Создали клиента
        $client = static::createClient();

        // Создаем запрос который будем отсылать
        $client->request('GET','/book/category');

        // getResponse - Объект ответа, getContent - содержимое, т.е фактически json который нам вернул контролер
        $responseContent = $client->getResponse()->getContent();

        // Проверяем что код ответа 200
        $this->assertResponseIsSuccessful();

        //
        $this->assertJsonStringEqualsJsonFile(
            __DIR__ . '/responses/BookCategoryControllerTest_testCategories.json'
                      ,$responseContent);


    }
}
