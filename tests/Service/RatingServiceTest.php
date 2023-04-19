<?php

namespace App\Tests\Service;

use App\Repository\ReviewRepository;
use App\Service\BookService;
use App\Service\RatingService;
use App\Tests\AbstractTestCase;
use PHPUnit\Framework\TestCase;

class RatingServiceTest extends AbstractTestCase
{
    private ReviewRepository $reviewRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reviewRepository = $this->createMock(ReviewRepository::class);
    }

    // По скольку для того чтобы получить ожидаемое число 0, в тесте ниже , нам нужно всего лишь скопировать одни и те же
    // значения мы создаем датаПровайдер
    public function provider()
    {   // Определяем массивы для набора данных
        //
        return [
            // Массив данных для одного тест кейса
            [25,20,1.25],
            // Следом за ним идет набор данных, но уже для другого тест кейса, уже с другими данными
            [0,5,0],

        ];
    }


    // Аргументы нам необходимо указать в нашем методе определив дата провайдеры (выше), provider - имя провайдера
    /**
     * @dataProvider provider
     */
    // Первый аргумент
    public function testCalcReviewRatingForBook(int $repositoryRatingSum, int $total, float $expectedRating)
    {
        $this->reviewRepository->expects($this->once())
            ->method('getBookTotalRatingSum')
            ->with(1)->willReturn($repositoryRatingSum);

        $actual = (new RatingService($this->reviewRepository))->calcReviewRatingForBook(1,$total);

        $this->assertEquals($expectedRating, $actual);
    }


    public function testCalcReviewRatingForBookWithZero()
    {
        $this->reviewRepository->expects($this->never())
            ->method('getBookTotalRatingSum');

        $actual = (new RatingService($this->reviewRepository))->calcReviewRatingForBook(1,0);

        $this->assertEquals(0, $actual);
    }




}
