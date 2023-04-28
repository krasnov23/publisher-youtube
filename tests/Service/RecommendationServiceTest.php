<?php

namespace src\Service;

use App\Entity\Book;
use App\Models\RecommendedBook;
use App\Models\RecommendedBookListResponse;
use App\Repository\BookRepository;
use App\Service\Recommendation\Model\RecommendationItem;
use App\Service\Recommendation\Model\RecommendationResponse;
use App\Service\Recommendation\RecommendationApiService;
use App\Service\RecommendationService;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\TestCase;

class RecommendationServiceTest extends AbstractTestCase
{

    private BookRepository $bookRepository;

    private RecommendationApiService $recommendationApiService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->bookRepository = $this->createMock(BookRepository::class);
        $this->recommendationApiService = $this->createMock(RecommendationApiService::class);
    }

    public function dataProvider(): array
    {
        return [
          // Исходное описание и то в которое оно должно превратиться
          ['short description', 'short description'],

          [
           <<<EOF
begin long description long description
long description long description long long description
long description long description long long description
description

EOF,
           <<<EOF
begin long description long description
long description long description long long description
long description long description long long descript...
EOF,
    ]
        ];
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetRecommendationsBook(string $actualDescription, string $expectedDescription): void
    {
        $entity = (new Book())->setImage('image')->setSlug('slug')
            ->setTitle('title')->setDescription($actualDescription);

        $this->setEntityId($entity,2);

        $this->bookRepository->expects($this->once())
            ->method('findBooksByIds')
            ->with([2])->willReturn([$entity]);

        // with 1 изначально мы заказываем рекомендации к айдишнику один и один из рекомендаций
        // к этому айди будет айдишник два
        $this->recommendationApiService->expects($this->once())
            ->method('getRecommendationByBookId')
            ->with(1)->willReturn(new RecommendationResponse(1,12345,
                [new RecommendationItem(2)]));

        $expected = new RecommendedBookListResponse([
            (new RecommendedBook())->setId(2)->setTitle('title')->setSlug('slug')->setImage('image')
            ->setShortDescription($expectedDescription)
        ]);

        $this->assertEquals($expected,$this->createService()->getRecommendationsByBookId(1));
    }


    private function createService()
    {
        return new RecommendationService($this->bookRepository,$this->recommendationApiService);
    }
}
