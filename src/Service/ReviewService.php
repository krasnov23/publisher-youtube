<?php

namespace App\Service;

use App\Entity\Review;
use App\Models\ReviewModel;
use App\Models\ReviewPage;
use App\Repository\ReviewRepository;

class ReviewService
{
    private const PAGE_LIMIT = 5;

    public function __construct(private ReviewRepository $reviewRepository)
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

        $paginator = $this->reviewRepository->getPageByBookId($id,$offset,self::PAGE_LIMIT);

        $ratingSum = $this->reviewRepository->getBookTotalRatingSum($id);

        // Paginator реализует интерфейс Countable поэтому мы можем применить просто Count и получим всё количество комментариев
        $total = count($paginator);
        $rating = 0;

        if ($total > 0)
        {
            $rating = $ratingSum / $total;
        }

        return (new ReviewPage())
            ->setRating($rating)
            ->setTotal($total)->setPage($page)
            ->setAmountPerPage(self::PAGE_LIMIT)
            // ceil - округляет до верхнего значения
            ->setAmountOfPages(ceil($total / self::PAGE_LIMIT))
            // Поскольку стандартные функции по типу array_map не умеют работать с итератерами,
            // Они умеют работать только с массивами. В данном случае мы получаем getArrayCopy, т.к метод
            // getPageByBookId возвращает нам Pagiantor, а не array
            ->setItems(array_map([$this,'map'],$paginator->getIterator()->getArrayCopy()));
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