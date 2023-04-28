<?php

namespace App\Tests\Controller;

use App\Entity\Book;
use App\Tests\AbstractControllerTest;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Hoverfly\Client as HoverflyClient;
use Hoverfly\Model\RequestFieldMatcher;
use Hoverfly\Model\Response;

class RecommendationControllerTest extends AbstractControllerTest
{
    private HoverflyClient $hoverfly;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpHoverfly();

    }

    private function setUpHoverfly(): void
    {
        // Инициализируем его переменной которую указали в .env.test
        $this->hoverfly = new HoverflyClient(['base_uri' => $_ENV['HOVERFLY_API']]);

        $this->hoverfly->deleteJournal();

        $this->hoverfly->deleteSimulation();
    }

    public function testRecommendationsByBookId(): void
    {
        $bookId = $this->createBook();
        $requestedId = 123;

        $this->hoverfly->simulate(
            $this->hoverfly->buildSimulation()
                ->service()
                ->get(new RequestFieldMatcher('/api/v1/book/'.$requestedId.'/recommendations',
                    RequestFieldMatcher::GLOB))
                ->headerExact('Authorization', 'Bearer test')
                ->willReturn(Response::json([
                    'ts' => 12345,
                    'id' => $requestedId,
                    'recommendations' => [['id' => $bookId]]
                ]))
        );

        $this->client->request('GET', '/api/v1/book/123/recommendations');

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);

        // Проверяем что код ответа 200
        $this->assertResponseIsSuccessful();

        $this->assertJsonDocumentMatchesSchema($responseContent,[
            'type' => 'object',
            'required' => ['items'],
            'properties' => [
                'items' => [
                    // Дословно как тип array, который будет состоять из объектов, т.е items как будет состоять из
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'required' => ['id','slug','title','image','shortDescription'],
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            // В данном случае не стоит путать с рейтингом указанным выше, тут рейтинг конкретного отзыва от 1 до 5
                            'slug' => ['type' => 'string'],
                            'title' => ['type' => 'string'],
                            'image' => ['type' => 'string'],
                            'shortDescription' => ['type' => 'string'],
                        ]
                    ]
                ]
            ]
        ]);
    }

    private function createBook(): int
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

        $this->em->flush();

        return $book->getId();
    }

}