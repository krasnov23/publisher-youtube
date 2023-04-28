<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Entity\Review;
use App\Tests\AbstractControllerTest;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class ReviewControllerTest extends AbstractControllerTest
{

    public function testReviews(): void
    {
        $book = $this->createBook();

        $this->createReview($book);

        $this->em->flush();

        $this->client->request('GET', 'api/v1/book/' . $book->getId() . '/reviews');

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Проверяем что код ответа 200
        $this->assertResponseIsSuccessful();
        $this->assertJsonDocumentMatchesSchema($responseContent,[
            'type' => 'object',
            'required' => ['items','rating','page','amountOfPages','amountPerPage','total'],
            'properties' => [
                // Когда мы хотим отдать float рейтинг - флоат и он проходит json сериализацию, php может преобразовать его в int
                // например если у нас рейтинг 4.0 php может просто отдать 4. (Это можно поправить в сериализаторе)
                // но нам это не критично, если он по дефолту сериализует ничего страшного сериализует (поэтому в данном случае
                // мы пишем что он не float не integer, а именно number.(т.е number - включает в себя и float и int)
                //
                'rating' => ['type' => 'number'],
                'page' => ['type' => 'integer'],
                'amountOfPages' => ['type' => 'number'],
                'amountPerPage' => ['type' => 'number'],
                'total' => ['type' => 'number'],
                'items' => [
                    // Дословно как тип array, который будет состоять из объектов, т.е items как будет состоять из
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id','content','author','rating','createdAt'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            // В данном случае не стоит путать с рейтингом указанным выше, тут рейтинг конкретного отзыва от 1 до 5
                            'rating' => ['type' => 'integer'],
                            'createdAt' => ['type' => 'integer'],
                            'content' => ['type' => 'string'],
                            'author' => ['type' => 'string'],
                        ]
                    ]
                ]
            ]
        ]);


    }

    private function createBook(): Book
    {
        $book = (new Book())
            ->setTitle('Test Book')
            ->setImage('test.png')
            ->setMeap(true)
            ->setIsbn("123123")
            ->setDescription('test')
            ->setPublicationData(new DateTimeImmutable())
            ->setAuthors(['V.Pupkin'])
            ->setCategories(new ArrayCollection([]))
            ->setSlug('test-book');

        $this->em->persist($book);

        return $book;
    }

    private function createReview(Book $book)
    {
        $this->em->persist((new Review())
        ->setAuthor('tester')
        ->setContent('test content')
        ->setCreatedAt(new DateTimeImmutable())
        ->setRating(5)
        ->setBook($book));
    }

}