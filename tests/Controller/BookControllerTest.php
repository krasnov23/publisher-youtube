<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Tests\AbstractControllerTest;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BookControllerTest extends AbstractControllerTest
{
    public function testBooksByCategories()
    {
        $categoryId = $this->createCategory();

        // Создаем запрос который будем отсылать
        $this->client->request('GET', 'api/v1/category/'. $categoryId . '/books');

        // getResponse - Объект ответа, getContent - содержимое, т.е фактически json который нам вернул контролер
        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Проверяем что код ответа 200
        $this->assertResponseIsSuccessful();

        //
        $this->assertJsonDocumentMatchesSchema($responseContent,[
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id','title','slug','authors','meap','publicationData'],
                        'properties' =>[
                        'id' => ['type' => 'integer'],
                        'title' => ['type' => 'string'],
                        'slug' => ['type' => 'string'],
                        'image' => ['type' => 'string'],
                        'publicationDate' => ['type' => 'integer'],
                        'meap' => ['type' => 'boolean'],
                        'authors' => ['type' => 'array',
                        'items' => ['type' =>'string']]
                        ]
                    ]
    ]]

        ]);
    }

    private function createCategory(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Device')->setSlug('device');

        $this->em->persist($bookCategory);

        $this->em->persist((new Book())->setTitle('Test Book')
            ->setImage('test.png')
            ->setMeap(true)
            ->setPublicationData(new \DateTime())
            ->setAuthors(['V.Pupkin'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book'));

        $this->em->flush();

        return $bookCategory->getId();
    }
}
