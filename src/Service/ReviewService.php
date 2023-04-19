<?php

namespace App\Service;

use App\Entity\Review;
use App\Models\ReviewModel;
use App\Models\ReviewPage;
use App\Repository\ReviewRepository;

class ReviewService
{
    private const PAGE_LIMIT = 5;

    public function __construct(private ReviewRepository $reviewRepository,private RatingService $ratingService)
    {

    }

    // Клиент будет передавать страницу с отзывами, и айди книги, а мы будем возвращать ему одну страницу из набора
    public function getReviewPageByBookId(int $id,int $page): ReviewPage
    {
        // Берем страницу вычитаем из нее 1, сравниваем с нулем и выбираем то что больше
        // Если нам передадут -3 страницу например , т.е страницу с отрицательным значением он просто выдаст 0
        // Например нам передали 3ю страницу, 3-1 = 2 * 5 (количество на странице) = 10 (т.е страница у нас начнется с 10ого
        // элемента, поэтому первый элемент который мы получим будет 11)
        $offset = max($page - 1,0) * self::PAGE_LIMIT;

        // Получает массив сущностей книг ограниченных страницей книги и лимитов на эту страницу равным 5
        $paginator = $this->reviewRepository->getPageByBookId($id,$offset,self::PAGE_LIMIT);

        // Массив Items создан для того чтобы проще шло тестирование
        $items = [];

        // в цикле каждый отзыв о книги перемапливается в модель с помощью метода map указанного ниже
        foreach ($paginator as $item)
        {
            $items[] = $this->map($item);
        }

        // Paginator реализует интерфейс Countable поэтому мы можем применить просто Count и получим всё количество комментариев
        $total = count($paginator);

        $rating = $this->ratingService->calcReviewRatingForBook($id,$total);

        return (new ReviewPage())
            ->setRating($rating)
            ->setTotal($total)->setPage($page)
            ->setAmountPerPage(self::PAGE_LIMIT)
            // ceil - округляет до верхнего значения
            ->setAmountOfPages(ceil($total / self::PAGE_LIMIT))
            // Поскольку стандартные функции по типу array_map не умеют работать с итератерами,
            // Они умеют работать только с массивами. В данном случае мы получаем getArrayCopy, т.к метод
            // getPageByBookId возвращает нам Paginator, а не array
            // Возвращает массив ReviewModelей
            ->setItems($items);
    }

    private function map(Review $review): ReviewModel
    {
        return (new ReviewModel())
            ->setId($review->getId())
            ->setRating($review->getRating())
            ->setCreatedAt($review->getCreatedAt()->getTimestamp())
            ->setAuthor($review->getAuthor())
            ->setContent($review->getContent());
    }




}