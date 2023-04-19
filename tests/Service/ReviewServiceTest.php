<?php

namespace App\Tests\Service;

use App\Entity\Review;
use App\Models\ReviewModel;
use App\Models\ReviewPage;
use App\Repository\ReviewRepository;
use App\Service\RatingService;
use App\Service\ReviewService;
use App\Tests\AbstractTestCase;
use ArrayIterator;
use DateTimeImmutable;


class ReviewServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    private RatingService $ratingService;

    private const BOOK_ID = 1;

    private const PER_PAGE = 5;

    public function dataProvider(): array
    {
        // Передаем страницу и офсет (офсет - номер элемента с которого начинается отсчет в данном случае массива отзывов)
        return [
            [0,0],
            [-1,0],
            [-20,0]
        ];
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
        $this->ratingService = $this->createMock(RatingService::class);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testGetReviewPageByBookIdInvalidPage(int $page,int $offset):void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID,0)
            ->willReturn(0.0);

        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID,$offset,self::PER_PAGE)
            // Поскольку в методе getPageByBookId мы поменяли возвращаемое значение и теперь
            // мы обходим пагинатор через foreach его можно вполне очень просто эмулировать итератором
            ->willReturn(new ArrayIterator());


        $service = new ReviewService($this->reviewRepository,$this->ratingService);

        $expected = (new ReviewPage())
        // Тотал 0 т.к нет отзывов у книге, рейтинг тоже
        ->setTotal(0)
        ->setRating(0)
        // page будет равна отправляемой книге, если нам передали -1, мы -1 и должны передать
        ->setPage($page)
        ->setAmountOfPages(0)
        ->setAmountPerPage(self::PER_PAGE)
        ->setItems([]);

        $this->assertEquals($expected,$service->getReviewPageByBookId(self::BOOK_ID,$page));
    }

    public function testGetReviewPageByBookId():void
    {
        $this->ratingService->expects($this->once())
            ->method('calcReviewRatingForBook')
            ->with(self::BOOK_ID,1)
            ->willReturn(4.0);

        // В данном случае поскольку reviewRepository возвращает нам не пустой массив то есть (ArrayIterator),
        // Нам нужно подготовить то что он вернет
        $entity = (new Review())->setAuthor('Tester')->setContent('Test Content')
            ->setCreatedAt(new DateTimeImmutable('2020-10-10'))->setRating(4);


        $this->setEntityId($entity, 1);

        $this->reviewRepository->expects($this->once())
            ->method('getPageByBookId')
            ->with(self::BOOK_ID,0,self::PER_PAGE)
            // Поскольку в методе getPageByBookId мы поменяли возвращаемое значение и теперь
            // мы обходим пагинатор через foreach его можно вполне очень просто иммулировать итератором
            ->willReturn(new ArrayIterator([$entity]));


        $service = new ReviewService($this->reviewRepository,$this->ratingService);

        $expected = (new ReviewPage())
            // Тотал 0 т.к нет отзывов у книге, рейтинг тоже
            ->setTotal(1)
            ->setRating(4)
            // page будет равна отправляемой книге, если нам передали -1, мы -1 и должны передать
            ->setPage(1)
            ->setAmountOfPages(1)
            ->setAmountPerPage(self::PER_PAGE)
            ->setItems([(new ReviewModel())->setId(1)->setAuthor('Tester')->setContent('Test Content')
            ->setCreatedAt(1602288000)->setRating(4)]);

        $this->assertEquals($expected,$service->getReviewPageByBookId(self::BOOK_ID,1));
    }


}
