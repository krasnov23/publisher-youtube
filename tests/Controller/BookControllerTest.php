<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\BookCategory;
use App\Entity\BookFormat;
use App\Entity\BookToBookFormat;
use App\Tests\AbstractControllerTest;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

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

        // Сравниваем пришедшее значение со схемой ответа
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


    public function testBookById(): void
    {
        $bookId = $this->createBook();

        $this->client->request('GET','/api/v1/book/' . $bookId);

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();

        $this->assertJsonDocumentMatchesSchema($responseContent,[
            'type' => 'object',
            'required' => ['id','title','slug','authors','meap','publicationData','rating','reviews','categories','formats'],
            'properties' => [
                'id' => ['type' => 'integer'],
                'title' => ['type' => 'string'],
                'slug' => ['type' => 'string'],
                'image' => ['type' => 'string'],
                'publicationDate' => ['type' => 'integer'],
                'meap' => ['type' => 'boolean'],
                'authors' => [
                    'type' => 'array',
                    'items' => ['type' =>'string'],
                ],
                'rating' => ['type' => 'number'],
                'reviews' => ['type' => 'integer'],
                'categories' => [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id','title','slug'],
                        'properties' => [
                            'title' => ['type' => 'string'],
                            'slug' => ['type' => 'string'],
                            'id' => ['type' => 'integer']
                        ]
                    ]
                ]

            ]
        ]);


    }

    private function createCategory(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Device')->setSlug('device');

        $this->em->persist($bookCategory);

        $this->em->persist((new Book())->setTitle('Test Book')
            ->setImage('test.png')
            ->setMeap(true)
            ->setIsbn("123123")
            ->setDescription('test')
            ->setPublicationData(new DateTimeImmutable())
            ->setAuthors(['V.Pupkin'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book'));

        $this->em->flush();

        return $bookCategory->getId();
    }

    private function createBook(): int
    {
        $bookCategory = (new BookCategory())->setTitle('Device')->setSlug('device');
        $this->em->persist($bookCategory);

        $format = (new BookFormat())->setTitle('format')->setDescription('description format')
            ->setComment(null);
        $this->em->persist($format);

        $book = (new Book())->setTitle('Test Book')
            ->setImage('test.png')
            ->setMeap(true)
            ->setIsbn("123123")
            ->setDescription('test')
            ->setPublicationData(new DateTimeImmutable())
            ->setAuthors(['V.Pupkin'])
            ->setCategories(new ArrayCollection([$bookCategory]))
            ->setSlug('test-book');
        $this->em->persist($book);

        $joinToBookFormat = (new BookToBookFormat())->setPrice(123.55)
            ->setFormat($format)->setDiscountPercent(5)
            ->setBook($book);
        $this->em->persist($joinToBookFormat);

        $this->em->flush();

        return $book->getId();
    }
}
