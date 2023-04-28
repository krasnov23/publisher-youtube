<?php

namespace App\Service;

use App\Repository\ReviewRepository;

class RatingService
{

    public function __construct(private ReviewRepository $reviewRepository)
    {
    }


    public function calcReviewRatingForBook(int $id): Rating
    {

        // Считаем количество отзывов у этой книги по ID
        $total = $this->reviewRepository->countByBookId($id);

        // Возвращает средний рейтинг книги в случае если есть хотябы один отзыв
        $rating = $total > 0 ? $this->reviewRepository->getBookTotalRatingSum($id) / $total : 0;

        // возвращает объект из которого соответственно мы уже будем получать total и rating
        return new Rating($total, $rating);
    }



}