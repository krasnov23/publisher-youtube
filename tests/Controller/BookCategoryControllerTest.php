<?php

namespace App\Tests\Controller;

use App\Tests\AbstractControllerTest;
use App\Tests\Service\MockUtils;

class BookCategoryControllerTest extends AbstractControllerTest
{
    public function testIndex()
    {

        $this->em->persist(MockUtils::createBookCategory());

        $this->em->flush();

        // Создаем запрос который будем отсылать
        $this->client->request('GET', 'api/v1/book/categories');

        // getResponse - Объект ответа, getContent - содержимое, т.е фактически json который нам вернул контролер

        // когда мы получаем тело ответа нам его необходимо превратить в массив (с помощью json_decode)
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Проверяем что код ответа 200
        $this->assertResponseIsSuccessful();

        // При установке пакета helmich/phpunit-json-assert у нас появился метод который указан снизу
        // Сравниваем наш контент с json схемой
        $this->assertJsonDocumentMatchesSchema($responseContent,[
            // Описывает какого типа у нас json элемент в данном случае у нас объект
            'type' => 'object',
            // Какие обязательные поля в данном объекте мы ждем, в данном случае у нас только одно поле items
            'required' => ['items'],
            // Сверху мы задикларировали наше обязательное поле, а в поле ниже мы уточняем какого оно типа и что в себя включает
            'properties' => [
                'items' => [
                    // items - это массив
                    'type' => 'array',
                    // Из чего состоит массив
                    'items' => [
                        'type' => 'object',
                        // В каждом объекте мы указываем какие обязательные свойства мы ждем
                        'required' => ['id','title','slug'],
                        'properties' => [
                            // какие есть свойства в каждой сущности и какие у них типы
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer']
                        ]
                    ]
                ]
            ]
        ]);


    }
}
